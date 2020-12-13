<?php

namespace App\Repository;

use App\Entity\ExpenseTag;
use App\Library\Repository\BaseRepository;
use Doctrine\Persistence\ManagerRegistry;

class ExpenseTagRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ExpenseTag::class);
    }

    public function getFilterFields(): array
    {
        return ['name'];
    }
}
