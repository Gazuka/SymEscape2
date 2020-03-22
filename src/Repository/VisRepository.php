<?php

namespace App\Repository;

use App\Entity\Vis;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Vis|null find($id, $lockMode = null, $lockVersion = null)
 * @method Vis|null findOneBy(array $criteria, array $orderBy = null)
 * @method Vis[]    findAll()
 * @method Vis[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VisRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Vis::class);
    }

    // /**
    //  * @return Vis[] Returns an array of Vis objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('v.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Vis
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
