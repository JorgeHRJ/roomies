<?php

namespace App\Repository;

use App\Entity\ExpenseUser;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ExpenseUser|null find($id, $lockMode = null, $lockVersion = null)
 * @method ExpenseUser|null findOneBy(array $criteria, array $orderBy = null)
 * @method ExpenseUser[]    findAll()
 * @method ExpenseUser[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ExpenseUserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ExpenseUser::class);
    }

    // /**
    //  * @return ExpenseUser[] Returns an array of ExpenseUser objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ExpenseUser
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
