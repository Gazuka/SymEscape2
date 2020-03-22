<?php

namespace App\Repository;

use App\Entity\Commut;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Commut|null find($id, $lockMode = null, $lockVersion = null)
 * @method Commut|null findOneBy(array $criteria, array $orderBy = null)
 * @method Commut[]    findAll()
 * @method Commut[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommutRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Commut::class);
    }

    // /**
    //  * @return Commut[] Returns an array of Commut objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Commut
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
