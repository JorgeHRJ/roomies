<?php

namespace App\Repository;

use App\Entity\Home;
use App\Entity\User;
use App\Library\Repository\BaseRepository;
use Doctrine\Persistence\ManagerRegistry;

class HomeRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Home::class);
    }

    public function findByUser(User $user): array
    {
        $qb = $this->createQueryBuilder('h');
        $qb->join('h.users', 'u')
            ->where('u.id = :userId')
            ->setParameter('userId', $user->getId());

        return $qb->getQuery()->getResult();
    }

    public function getFilterFields(): array
    {
        return [];
    }
}
