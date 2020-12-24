<?php

namespace App\Service;

use App\Entity\Expense;
use App\Entity\ExpenseUser;
use App\Entity\Home;
use App\Entity\User;
use App\Library\Cache\DebtsCacheItem;
use App\Library\Repository\BaseRepository;
use App\Library\Service\BaseService;
use App\Repository\ExpenseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\Cache\CacheInterface;

class ExpenseService extends BaseService
{
    /** @var ExpenseRepository */
    private $repository;

    /** @var CacheInterface */
    private $cache;

    public function __construct(
        EntityManagerInterface $entityManager,
        LoggerInterface $logger,
        ContextService $contextService,
        CacheInterface $cache
    ) {
        parent::__construct($entityManager, $logger, $contextService);
        $this->repository = $entityManager->getRepository(Expense::class);
        $this->cache = $cache;
    }

    /**
     * @param Expense $entity
     * @return Expense
     * @throws \Exception
     */
    public function create($entity)
    {
        $expenseUsers = $entity->getExpenseUsers();
        $amountPerPerson = round(($entity->getAmount() / $expenseUsers->count()), 2);

        /** @var ExpenseUser $expenseUser */
        foreach ($expenseUsers as $expenseUser) {
            if ($expenseUser->getUser()->getId() === $entity->getPaidBy()->getId()) {
                $expenseUser->setStatus(ExpenseUser::PAID_STATUS);
            }

            if ($expenseUser->getStatus() === ExpenseUser::PENDING_STATUS) {
                $expenseUser->setPaidAt(null);
            }

            $expenseUser->setAmount((string) $amountPerPerson);
        }

        $entity = parent::create($entity);

        $this->resetDebts();

        return $entity;
    }

    public function getDebts(): array
    {
        $key = sprintf('%s_%d', DebtsCacheItem::DEBTS_CACHE_KEY, $this->contextService->getHome()->getId());

        return $this->cache->get($key, new DebtsCacheItem($this, $this->contextService, $this->entityManager));
    }

    public function resetDebts(): void
    {
        $key = sprintf('%s_%d', DebtsCacheItem::DEBTS_CACHE_KEY, $this->contextService->getHome()->getId());
        $this->cache->delete($key);
        $this->cache->get($key, new DebtsCacheItem($this, $this->contextService, $this->entityManager));
    }

    /**
     * @return Expense[]|array
     */
    public function getWithExpenseUsers(): array
    {
        return $this->repository->findWithExpenseUsers($this->contextService->getHome());
    }

    /**
     * @param Home $home
     * @param int $id
     * @return Expense|null
     */
    public function getByIdAndHome(Home $home, int $id): ?Expense
    {
        return $this->repository->findByIdAndHome($home, $id);
    }

    public function getSortFields(): array
    {
        return ['id', 'title', 'amount'];
    }

    public function getRepository(): BaseRepository
    {
        return $this->repository;
    }
}
