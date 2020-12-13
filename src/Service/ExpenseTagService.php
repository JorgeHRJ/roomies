<?php

namespace App\Service;

use App\Entity\Expense;
use App\Entity\ExpenseTag;
use App\Library\Repository\BaseRepository;
use App\Library\Service\BaseService;
use App\Repository\ExpenseTagRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class ExpenseTagService extends BaseService
{
    /** @var ExpenseTagRepository */
    private $repository;

    public function __construct(
        EntityManagerInterface $entityManager,
        LoggerInterface $logger,
        ContextService $contextService
    ) {
        parent::__construct($entityManager, $logger, $contextService);
        $this->repository = $entityManager->getRepository(ExpenseTag::class);
    }

    /**
     * @param Expense $expense
     * @param array|string[] $tags
     */
    public function handleExpenseTags(Expense $expense, array $tags): void
    {
        $home = $expense->getHome();

        foreach ($tags as $tag) {
            $expenseTag = $this->getByName($tag);
            if (!$expenseTag instanceof ExpenseTag) {
                $expenseTag = new ExpenseTag();
                $expenseTag->setName($tag);
                $expenseTag->setHome($home);

                $this->entityManager->persist($expenseTag);
            }

            $expense->addTag($expenseTag);
        }

        $this->entityManager->flush();
    }

    /**
     * Search Expense Tags and return their names
     *
     * @param string $query
     * @return array
     */
    public function search(string $query): array
    {
        $tags = $this->getAll($query);

        return array_map(function (ExpenseTag $tag) {
            return $tag->getName();
        }, $tags['data']);
    }

    public function getByName(string $tagName): ?ExpenseTag
    {
        return $this->repository->findOneBy(['name' => $tagName]);
    }

    public function getSortFields(): array
    {
        return [];
    }

    public function getRepository(): BaseRepository
    {
        return $this->repository;
    }
}
