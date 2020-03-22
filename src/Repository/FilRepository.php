<?php

namespace App\Repository;

use App\Entity\Fil;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Fil|null find($id, $lockMode = null, $lockVersion = null)
 * @method Fil|null findOneBy(array $criteria, array $orderBy = null)
 * @method Fil[]    findAll()
 * @method Fil[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FilRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Fil::class);
    }

    // /**
    //  * @return Fil[] Returns an array of Fil objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('f.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Fil
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
