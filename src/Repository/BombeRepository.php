<?php

namespace App\Repository;

use App\Entity\Bombe;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Bombe|null find($id, $lockMode = null, $lockVersion = null)
 * @method Bombe|null findOneBy(array $criteria, array $orderBy = null)
 * @method Bombe[]    findAll()
 * @method Bombe[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BombeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Bombe::class);
    }

    // /**
    //  * @return Bombe[] Returns an array of Bombe objects
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
    public function findOneBySomeField($value): ?Bombe
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
