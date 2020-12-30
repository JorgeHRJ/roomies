<?php

namespace App\Library\Cache;

use App\Entity\Expense;
use App\Entity\ExpenseUser;
use App\Entity\User;
use App\Repository\ExpenseRepository;
use App\Service\ContextService;
use App\Service\ExpenseService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Cache\CacheItemInterface;
use Symfony\Contracts\Cache\CallbackInterface;
use Symfony\Contracts\Cache\ItemInterface;

class DebtsCacheItem implements CallbackInterface
{
    const DEBTS_CACHE_KEY = 'debts';
    const DEBTS_CACHE_EXPIRATION = 3600;

    /** @var ExpenseService */
    private $expenseService;

    /** @var ContextService */
    private $contextService;

    /** @var EntityManagerInterface */
    private $entityManager;

    public function __construct(
        ExpenseService $expenseService,
        ContextService $contextService,
        EntityManagerInterface $entityManager
    ) {
        $this->expenseService = $expenseService;
        $this->contextService = $contextService;
        $this->entityManager = $entityManager;
    }

    /**
     * @param CacheItemInterface|ItemInterface $item The item to compute the value for
     * @param bool $save Should be set to false when the value should not be saved in the pool
     *
     * @return mixed The computed value for the passed item
     */
    public function __invoke(CacheItemInterface $item, bool &$save)
    {
        $item->expiresAfter(self::DEBTS_CACHE_EXPIRATION);

        $expenses = $this->expenseService->getWithExpenseUsers();

        $home = $this->contextService->getHome();
        $debts = $this->getInitialDebts($home->getUsers()->toArray());

        return $this->getDebtsFromExpenses($debts, $expenses);
    }

    /**
     * Get initial debts array with the users involved
     *
     * @param User[]|array $users
     * @return array
     */
    private function getInitialDebts(array $users): array
    {
        $usersData = [];
        foreach ($users as $user) {
            $usersData[] = ['user_id' => $user->getId(), 'user_name' => $user->getName()];
        }

        $debts = [];
        foreach ($usersData as $userData) {
            $userId = $userData['user_id'];
            $restUsersData = array_filter($usersData, function ($userDataTemp) use ($userId) {
                return $userId !== $userDataTemp['user_id'];
            });

            foreach ($restUsersData as $restUserData) {
                $debts[$userId][$restUserData['user_id']] = [
                    'amount' => 0,
                    'user_name' => $restUserData['user_name']
                ];
            }
        }

        return $debts;
    }

    /**
     * Get debts array for the users involved from the expenses and a given debts array
     *
     * @param array $debts
     * @param Expense[]|array $expenses
     * @return array
     */
    private function getDebtsFromExpenses(array $debts, array $expenses): array
    {
        //dump($expenses);die();
        foreach ($expenses as $expense) {
            $paidById = $expense->getPaidBy()->getId();
            foreach ($expense->getExpenseUsers() as $expenseUser) {
                $expenseUserId = $expenseUser->getUser()->getId();
                if ($expenseUserId === $paidById) {
                    continue;
                }

                if ($expenseUser->getStatus() === ExpenseUser::PAID_STATUS) {
                    continue;
                }

                $debts[$paidById][$expenseUserId]['amount'] += $expenseUser->getAmount();
                $debts[$expenseUserId][$paidById]['amount'] -= $expenseUser->getAmount();
            }
        }
        //dump($debts);die();
        return $debts;
    }
}
