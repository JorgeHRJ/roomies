<?php

namespace App\Repository;

use App\Entity\Expense;
use App\Entity\Home;
use App\Library\Repository\BaseRepository;
use Doctrine\Persistence\ManagerRegistry;

class ExpenseRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Expense::class);
    }

    /**
     * @param Home $home
     * @return Expense[]|array
     */
    public function findWithExpenseUsers(Home $home): array
    {
        $alias = 'e';

        $qb = $this->createQueryBuilder($alias);
        $qb
            ->join(sprintf('%s.expenseUsers', $alias), 'eu');

        $this->setHomeRestriction($alias, $qb, $home);

        return $qb->getQuery()->getResult();
    }

    public function getFilterFields(): array
    {
        return ['title', 'description'];
    }
}
