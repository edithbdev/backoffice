<?php

namespace App\Repository;

use App\Entity\FrontendLanguage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<FrontendLanguage>
 *
 * @method FrontendLanguage|null find($id, $lockMode = null, $lockVersion = null)
 * @method FrontendLanguage|null findOneBy(array $criteria, array $orderBy = null)
 * @method FrontendLanguage[]    findAll()
 * @method FrontendLanguage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FrontendLanguageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FrontendLanguage::class);
    }

    public function add(FrontendLanguage $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(FrontendLanguage $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return FrontendLanguage[] Returns an array of FrontendLanguages objects
     */
    public function findAllFrontendLanguages()
    {
        $query = $this->createQueryBuilder('f')
            ->select('f', 'p')
            ->leftJoin('f.projects', 'p', 'WITH', 'p.deleted = 0')
            ->where('f.deleted = 0')
            ->orderBy('f.createdAt', 'DESC')
            ->getQuery();

        return $query->getResult();
    }

    /**
     * @return FrontendLanguage[] Returns an array of FrontendLanguage objects
     */
    public function findBySearch(string $search): array
    {
        $query = $this->createQueryBuilder('f')
            ->select('f', 'p')
            ->leftJoin('f.projects', 'p', 'WITH', 'p.deleted = 0')
            ->where('f.deleted = 0')
            ->andWhere('f.name LIKE :search')
            ->setParameter('search', '%' . $search . '%')
            ->orderBy('f.name', 'ASC')
            ->getQuery();

        return $query->getResult();
    }
}
