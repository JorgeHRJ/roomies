<?php

namespace App\Library\Repository;

use App\Entity\Home;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

abstract class BaseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, string $entity)
    {
        parent::__construct($registry, $entity);
    }

    abstract public function getFilterFields(): array;

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
        $alias = 't';
        $qb = $this->createQueryBuilder($alias);

        $this->setFilter($alias, $qb, $filter);
        if ($home instanceof Home) {
            $this->setHomeRestriction($alias, $qb, $home);
        }

        if ($orderBy && count($orderBy)) {
            foreach ($orderBy as $field => $dir) {
                $qb->orderBy(sprintf('%s.%s', $alias, $field), $dir);
            }
        }

        if ($limit !== null && $offset !== null) {
            $qb->setFirstResult($offset);
            $qb->setMaxResults($limit);
        }

        return $qb->getQuery()->execute();
    }

    /**
     * @param string|null $filter
     * @param Home|null $home
     * @return int
     */
    public function getAllCount(?string $filter, Home $home = null)
    {
        $alias = 't';

        $qb = $this->createQueryBuilder($alias)->select(sprintf('count(%s.id)', $alias));

        $this->setFilter($alias, $qb, $filter);
        if ($home instanceof Home) {
            $this->setHomeRestriction($alias, $qb, $home);
        }

        try {
            return $qb->getQuery()->getSingleScalarResult();
        } catch (NonUniqueResultException $e) {
            return 0;
        } catch (NoResultException $e) {
            return 0;
        }
    }

    /**
     * @param string $alias
     * @param QueryBuilder $qb
     * @param string|null $filter
     */
    protected function setFilter(string $alias, QueryBuilder $qb, ?string $filter): void
    {
        if (!$filter) {
            return;
        }

        $filterFields = $this->getFilterFields();
        foreach ($filterFields as $field) {
            $qb->orWhere(sprintf('%s LIKE :%s_filter', sprintf('%s.%s', $alias, $field), $field));
            $qb->setParameter(sprintf('%s_filter', $field), sprintf('%%%s%%', $filter));
        }
    }

    /**
     * @param string $alias
     * @param QueryBuilder $qb
     * @param Home $home
     */
    protected function setHomeRestriction(string $alias, QueryBuilder $qb, Home $home): void
    {
        $qb->andWhere(sprintf('%s.home = :homeId', $alias));
        $qb->setParameter('homeId', $home->getId());
    }
}
