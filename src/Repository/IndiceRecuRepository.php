<?php

namespace App\Repository;

use App\Entity\IndiceRecu;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method IndiceRecu|null find($id, $lockMode = null, $lockVersion = null)
 * @method IndiceRecu|null findOneBy(array $criteria, array $orderBy = null)
 * @method IndiceRecu[]    findAll()
 * @method IndiceRecu[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class IndiceRecuRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, IndiceRecu::class);
    }

    // /**
    //  * @return IndiceRecu[] Returns an array of IndiceRecu objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('i.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?IndiceRecu
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
