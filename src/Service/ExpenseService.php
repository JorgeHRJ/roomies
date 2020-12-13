<?php

namespace App\Service;

use App\Entity\Expense;
use App\Library\Repository\BaseRepository;
use App\Library\Service\BaseService;
use App\Repository\ExpenseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class ExpenseService extends BaseService
{
    /** @var ExpenseRepository */
    private $repository;

    public function __construct(
        EntityManagerInterface $entityManager,
        LoggerInterface $logger,
        ContextService $contextService
    ) {
        parent::__construct($entityManager, $logger, $contextService);
        $this->repository = $entityManager->getRepository(Expense::class);
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

        foreach ($expenseUsers as $expenseUser) {
            $expenseUser->setAmount((string) $amountPerPerson);
        }

        return parent::create($entity);
    }

    public function getSortFields(): array
    {
        return ['title', 'amount'];
    }

    public function getRepository(): BaseRepository
    {
        return $this->repository;
    }
}
