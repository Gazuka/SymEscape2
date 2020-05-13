<?php

namespace App\Repository;

use App\Entity\Boulon;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Boulon|null find($id, $lockMode = null, $lockVersion = null)
 * @method Boulon|null findOneBy(array $criteria, array $orderBy = null)
 * @method Boulon[]    findAll()
 * @method Boulon[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BoulonRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Boulon::class);
    }

    // /**
    //  * @return Boulon[] Returns an array of Boulon objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('b.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Boulon
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
