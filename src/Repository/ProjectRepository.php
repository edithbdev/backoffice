<?php

namespace App\Repository;

use App\Entity\Tool;
use App\Entity\Images;
use App\Entity\Project;
use App\Entity\Enum\Status;
use App\Entity\BackendLanguage;
use App\Entity\FrontendLanguage;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Project>
 *
 * @method Project|null find($id, $lockMode = null, $lockVersion = null)
 * @method Project|null findOneBy(array $criteria, array $orderBy = null)
 * @method Project[]    findAll()
 * @method Project[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProjectRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Project::class);
    }

    public function add(Project $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Project $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return Project[] Returns an array of Project objects
     */
    public function findAllProjects(): array
    {
        $status = [Status::Published, Status::Draft];

        $query = $this->createQueryBuilder('p')
            ->select('p', 'f', 'b', 't')
            ->leftJoin('p.frontendLanguages', 'f', 'WITH', 'f.deleted = 0')
            ->leftJoin('p.backendLanguages', 'b', 'WITH', 'b.deleted = 0')
            ->leftJoin('p.tools', 't', 'WITH', 't.deleted = 0')
            ->where('p.deleted = 0')
            ->andWhere('p.status IN (:status)')
            ->setParameter('status', $status)
            ->orderBy('p.id', 'DESC')
            ->getQuery();

        return $query->getResult();
    }

    /**
     * @return Project|null Returns a Project object
     * @param string $id
     */
    public function findProjectById($id)
    {
        $status = [Status::Published];

        $query = $this->createQueryBuilder('p')
            ->select('p', 'f', 'b', 't', 'i')
            ->leftJoin('p.frontendLanguages', 'f', 'WITH', 'f.deleted = 0')
            ->leftJoin('p.backendLanguages', 'b', 'WITH', 'b.deleted = 0')
            ->leftJoin('p.tools', 't', 'WITH', 't.deleted = 0')
            ->leftJoin('p.images', 'i', 'WITH', 'i.deleted = 0')
            ->where('p.id = :id')
            ->andWhere('p.deleted = 0')
            ->andWhere('p.status IN (:status)')
            ->setParameter('status', $status)
            ->setParameter('id', $id)
            ->getQuery();

        $result = $query->getResult()[0] ?? null;
        return $result;
    }

    /**
     * @return Project|null Returns a Project object
     * @param string $slug
     */
    public function findProjectBySlug($slug)
    {
        $status = [Status::Published];

        $query = $this->createQueryBuilder('p')
            ->select('p', 'f', 'b', 't', 'i')
            ->leftJoin('p.frontendLanguages', 'f', 'WITH', 'f.deleted = 0')
            ->leftJoin('p.backendLanguages', 'b', 'WITH', 'b.deleted = 0')
            ->leftJoin('p.tools', 't', 'WITH', 't.deleted = 0')
            ->leftJoin('p.images', 'i', 'WITH', 'i.deleted = 0')
            ->where('p.slug = :slug')
            ->andWhere('p.deleted = 0')
            ->andWhere('p.status IN (:status)')
            ->setParameter('status', $status)
            ->setParameter('slug', $slug)
            ->addOrderBy('f.name', 'ASC')
            ->addOrderBy('b.name', 'ASC')
            ->addOrderBy('t.name', 'ASC')
            ->getQuery();

        $result = $query->getResult()[0] ?? null;
        return $result;
    }

    /**
     * @return Project[] Returns an array of Project objects
     */
    public function findAllProjectsArchived()
    {
        $status = Status::Archived;

        $query = $this->createQueryBuilder('p')
            ->select('p', 'f', 'b', 't')
            ->leftJoin('p.frontendLanguages', 'f')
            ->leftJoin('p.backendLanguages', 'b')
            ->leftJoin('p.tools', 't')
            ->Where('p.status = :status')
            ->orwhere('p.deleted = 1')
            ->setParameter('status', $status)
            ->orderBy('p.createdAt', 'DESC')
            ->getQuery();

        return $query->getResult();
    }

    /**
     * @return Project[] Returns an array of Project objects
     * @param array<array-key, FrontendLanguage> $frontendLanguages
     * @param array<array-key, BackendLanguage> $backendLanguages
     * @param array<array-key, Tool> $tools
     */
    public function findSimilarProjects($frontendLanguages, $backendLanguages, $tools): array
    {
        $query = $this->createQueryBuilder('p')
            ->select('p', 'f', 'b', 't')
            ->leftJoin('p.frontendLanguages', 'f', 'WITH', 'f.deleted = 0')
            ->leftJoin('p.backendLanguages', 'b', 'WITH', 'b.deleted = 0')
            ->leftJoin('p.tools', 't', 'WITH', 't.deleted = 0')
            ->where('p.deleted = 0')
            ->andWhere('f.id IN (:frontendLanguages)')
            ->orWhere('b.id IN (:backendLanguages)')
            ->orWhere('t.id IN (:tools)')
            ->setParameter('frontendLanguages', $frontendLanguages)
            ->setParameter('backendLanguages', $backendLanguages)
            ->setParameter('tools', $tools)
            ->orderBy('p.createdAt', 'DESC')
            ->getQuery();

        return $query->getResult();
    }

    /**
     * @return Project[] Returns an array of Project objects
     */
    public function findBySearch(string $search): array
    {
        $query = $this->createQueryBuilder('p')
            ->select('p', 'f', 'b', 't')
            ->leftJoin('p.frontendLanguages', 'f', 'WITH', 'f.deleted = 0')
            ->leftJoin('p.backendLanguages', 'b', 'WITH', 'b.deleted = 0')
            ->leftJoin('p.tools', 't', 'WITH', 't.deleted = 0')
            ->where('p.deleted = 0')
            ->andWhere('p.name LIKE :search')
            ->orWhere('p.description LIKE :search')
            ->orWhere('f.name LIKE :search')
            ->orWhere('b.name LIKE :search')
            ->orWhere('t.name LIKE :search')
            ->orWhere('t.description LIKE :search')
            ->setParameter('search', '%' . $search . '%')
            ->orderBy('p.createdAt', 'DESC')
            ->getQuery();

        return $query->getResult();
    }
}
