<?php

namespace App\Repository;

use App\Entity\ObjetScenario;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method ObjetScenario|null find($id, $lockMode = null, $lockVersion = null)
 * @method ObjetScenario|null findOneBy(array $criteria, array $orderBy = null)
 * @method ObjetScenario[]    findAll()
 * @method ObjetScenario[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ObjetScenarioRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ObjetScenario::class);
    }

    // /**
    //  * @return ObjetScenario[] Returns an array of ObjetScenario objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('o.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ObjetScenario
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
