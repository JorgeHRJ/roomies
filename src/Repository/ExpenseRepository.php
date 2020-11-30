<?php

namespace App\Repository;

use App\Entity\Expense;
use App\Library\Repository\BaseRepository;
use Doctrine\Persistence\ManagerRegistry;

class ExpenseRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Expense::class);
    }

    public function getFilterFields(): array
    {
        return ['title', 'description'];
    }
}
