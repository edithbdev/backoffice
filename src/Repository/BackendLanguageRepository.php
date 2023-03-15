<?php

namespace App\Repository;

use App\Entity\BackendLanguage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<BackendLanguage>
 *
 * @method BackendLanguage|null find($id, $lockMode = null, $lockVersion = null)
 * @method BackendLanguage|null findOneBy(array $criteria, array $orderBy = null)
 * @method BackendLanguage[]    findAll()
 * @method BackendLanguage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BackendLanguageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BackendLanguage::class);
    }

    public function add(BackendLanguage $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(BackendLanguage $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return BackendLanguage[] Returns an array of BackendLanguages objects
     */
    public function findAllBackendLanguages()
    {
        $query = $this->createQueryBuilder('b')
            ->select('b', 'p')
            ->leftJoin('b.projects', 'p', 'WITH', 'p.deleted = 0')
            ->where('b.deleted = 0')
            ->orderBy('b.createdAt', 'DESC')
            ->getQuery();

        return $query->getResult();
    }

    /**
     * @return BackendLanguage[] Returns an array of BackendLanguage objects
     */
    public function findBySearch(string $search): array
    {
        $query = $this->createQueryBuilder('b')
            ->select('b', 'p')
            ->leftJoin('b.projects', 'p', 'WITH', 'p.deleted = 0')
            ->where('b.deleted = 0')
            ->andWhere('b.name LIKE :search')
            ->setParameter('search', '%' . $search . '%')
            ->orderBy('b.name', 'ASC')
            ->getQuery();

        return $query->getResult();
    }
}
