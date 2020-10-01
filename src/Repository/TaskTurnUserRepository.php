<?php

namespace App\Repository;

use App\Entity\TaskTurnUser;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TaskTurnUser|null find($id, $lockMode = null, $lockVersion = null)
 * @method TaskTurnUser|null findOneBy(array $criteria, array $orderBy = null)
 * @method TaskTurnUser[]    findAll()
 * @method TaskTurnUser[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TaskTurnUserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TaskTurnUser::class);
    }

    // /**
    //  * @return TaskTurnUser[] Returns an array of TaskTurnUser objects
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
    public function findOneBySomeField($value): ?TaskTurnUser
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
