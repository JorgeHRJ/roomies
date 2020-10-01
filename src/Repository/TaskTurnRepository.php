<?php

namespace App\Repository;

use App\Entity\TaskTurn;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TaskTurn|null find($id, $lockMode = null, $lockVersion = null)
 * @method TaskTurn|null findOneBy(array $criteria, array $orderBy = null)
 * @method TaskTurn[]    findAll()
 * @method TaskTurn[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TaskTurnRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TaskTurn::class);
    }

    // /**
    //  * @return TaskTurn[] Returns an array of TaskTurn objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?TaskTurn
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
