<?php

namespace App\Repository;

use App\Entity\Home;
use App\Entity\User;
use App\Library\Repository\BaseRepository;
use Doctrine\Persistence\ManagerRegistry;

class UserRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function getFilterFields(): array
    {
        return [];
    }

    /**
     * @param Home $home
     * @return User[]|array
     */
    public function getByHome(Home $home): array
    {
        $qb = $this->createQueryBuilder('u');
        $qb->join('u.homes', 'h')
            ->where('h.id = :homeId')
            ->setParameter('homeId', $home->getId())
            ->orderBy('u.name', 'ASC');

        return $qb->getQuery()->getResult();
    }
}
