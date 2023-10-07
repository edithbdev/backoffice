<?php

namespace App\Repository;

use App\Entity\Contact;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Contact>
 *
 * @method Contact|null find($id, $lockMode = null, $lockVersion = null)
 * @method Contact|null findOneBy(array $criteria, array $orderBy = null)
 * @method Contact[]    findAll()
 * @method Contact[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ContactRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Contact::class);
    }

    public function add(Contact $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Contact $entity, bool $flush = false): void
    {
        // $this->getEntityManager()->remove($entity);

        // if ($flush) {
        //     $this->getEntityManager()->flush();
        // }
        $this->getEntityManager()->persist($entity->setDeleted(true));

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
    /**
     * @return Contact[] Returns an array of Contact objects
     */
    public function findMessagesToArchive(): array
    {
        $query = $this->createQueryBuilder('c')
            ->select('c')
            ->where('c.deleted = :deleted')
            ->orWhere('c.isAnswered = :isAnswered')
            ->setParameter('deleted', true)
            ->setParameter('isAnswered', true)
            ->getQuery();

        return $query->getResult();
    }

    /**
     * Retrieves the number of unread messages.
     * @return bool|float|int|string|null The number of unread messages.
     */
    public function countUnreadMessages()
    {
        $query = $this->createQueryBuilder('c')
            ->select('COUNT(c)')
            ->where('c.isRead = :isRead')
            ->setParameter('isRead', false)
            ->getQuery();

        return $query->getSingleScalarResult();
    }

    /**
     * Retrieves the number of archived messages.
     * @return bool|float|int|string|null The number of archived messages.
     */
    public function countMessagesArchived()
    {
        $query = $this->createQueryBuilder('c')
            ->select('COUNT(c)')
            ->where('c.deleted = :deleted')
            ->orWhere('c.isAnswered = :isAnswered')
            ->setParameter('deleted', true)
            ->setParameter('isAnswered', true)
            ->getQuery();

        return $query->getSingleScalarResult();
    }
}
