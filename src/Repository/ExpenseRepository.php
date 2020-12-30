<?php

namespace App\Repository;

use App\Entity\Expense;
use App\Entity\Home;
use App\Library\Repository\BaseRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

class ExpenseRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Expense::class);
    }

    /**
     * @param string|null $filter
     * @param array|null $orderBy
     * @param int|null $limit
     * @param int|null $offset
     * @param Home|null $home
     *
     * @return mixed
     */
    public function getAll(
        string $filter = null,
        array $orderBy = null,
        int $limit = null,
        int $offset = null,
        Home $home = null
    ) {
        $alias = 'e';
        $qb = $this->createQueryBuilder($alias);

        $this->setJoins($alias, $qb);

        $this->setFilter($alias, $qb, $filter);
        if ($home instanceof Home) {
            $this->setHomeRestriction($alias, $qb, $home);
        }

        if (!empty($orderBy)) {
            foreach ($orderBy as $field => $dir) {
                $qb->orderBy(sprintf('%s.%s', $alias, $field), $dir);
            }
        }

        if ($limit !== null && $offset !== null) {
            $qb->setFirstResult($offset);
            $qb->setMaxResults($limit);
        }
        $qb->groupBy('e');

        dump($qb->getQuery()->execute());die();
    }

    /**
     * @param Home $home
     * @return Expense[]|array
     */
    public function findWithExpenseUsers(Home $home): array
    {
        $alias = 'e';

        $qb = $this->createQueryBuilder($alias);
        $qb->join(sprintf('%s.expenseUsers', $alias), 'eu');

        $this->setHomeRestriction($alias, $qb, $home);

        return $qb->getQuery()->getResult();
    }

    public function findByIdAndHome(Home $home, int $id): ?Expense
    {
        $alias = 'e';

        $qb = $this->createQueryBuilder($alias);
        $this->setJoins($alias, $qb);

        $qb
            ->addSelect('u')
            ->join('eu.user', 'u');

        $qb
            ->where(sprintf('%s.id = :id', $alias))
            ->setParameter('id', $id);

        $this->setHomeRestriction($alias, $qb, $home);

        try {
            return $qb->getQuery()->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            return null;
        }
    }

    public function getFilterFields(): array
    {
        return ['title', 'description'];
    }

    /**
     * @param string $alias
     * @param QueryBuilder $qb
     */
    private function setJoins(string $alias, QueryBuilder $qb): void
    {
        $qb
            ->addSelect('eu')
            ->addSelect('et')
            ->addSelect('pb')
            ->join(sprintf('%s.expenseUsers', $alias), 'eu')
            ->leftJoin(sprintf('%s.tags', $alias), 'et')
            ->join(sprintf('%s.paidBy', $alias), 'pb')
        ;
    }
}
