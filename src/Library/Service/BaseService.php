<?php

namespace App\Library\Service;

use App\Library\Repository\BaseRepository;
use App\Service\ContextService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Psr\Log\LoggerInterface;

abstract class BaseService
{
    /** @var EntityManagerInterface */
    protected $entityManager;

    /** @var LoggerInterface */
    protected $logger;

    /** @var ContextService */
    protected $contextService;

    public function __construct(
        EntityManagerInterface $entityManager,
        LoggerInterface $logger,
        ContextService $contextService
    ) {
        $this->entityManager = $entityManager;
        $this->logger = $logger;
        $this->contextService = $contextService;
    }

    abstract public function getSortFields(): array;
    abstract public function getRepository(): BaseRepository;

    /**
     * @param object $entity
     * @return object
     * @throws Exception
     */
    public function create($entity)
    {
        try {
            $this->entityManager->persist($entity);
            $this->entityManager->flush();

            $this->logger->info(sprintf('Created %s ID::%s', get_class($entity), $entity->getId()));

            return $entity;
        } catch (Exception $e) {
            $this->logger->error(sprintf('Error creating %s. Error: %s', get_class($entity), $e->getMessage()));

            throw $e;
        }
    }

    /**
     * @param object $entity
     * @return object
     * @throws Exception
     */
    public function edit($entity)
    {
        try {
            $this->entityManager->flush();

            $this->logger->info(sprintf('Updated %s ID::%s', get_class($entity), $entity->getId()));

            return $entity;
        } catch (Exception $e) {
            $this->logger->error(sprintf('Error updating %s. Error: %s', get_class($entity), $e->getMessage()));

            throw $e;
        }
    }

    /**
     * @param object $entity
     * @return object
     * @throws Exception
     */
    public function remove($entity)
    {
        try {
            $this->entityManager->remove($entity);
            $this->entityManager->flush();

            $this->logger->info(sprintf('Removed %s ID::%s', get_class($entity), $entity->getId()));

            return $entity;
        } catch (Exception $e) {
            $this->logger->error(sprintf('Error removing %s. Error: %s', get_class($entity), $e->getMessage()));

            throw $e;
        }
    }

    /**
     * @param int $id
     *
     * @return object|null
     */
    public function get(int $id)
    {
        return $this->getRepository()->findOneBy(['id' => $id]);
    }

    /**
     * @param string|null $filter
     * @param int|null $page
     * @param int|null $limit
     * @param string $sort
     * @param string $dir
     *
     * @return array
     */
    public function getAll(
        string $filter = null,
        int $page = null,
        int $limit = null,
        string $sort = '',
        string $dir = ''
    ): array {
        $orderBy = ['id' => 'DESC'];
        if ($sort && in_array($sort, $this->getSortFields())) {
            $orderBy = [(string) $sort => $dir ? strtoupper($dir) : 'ASC'];
        }

        $offset = $page !== null && $limit !== null ? ($page - 1) * $limit : null;

        $entities = $this->getRepository()->getAll(
            $filter,
            $orderBy,
            $limit,
            $offset,
            $this->contextService->getHome()
        );
        $total = $this->getRepository()->getAllCount($filter, $this->contextService->getHome());

        return ['total' => $total, 'data' => $entities];
    }

    /**
     * @return int
     */
    public function getAllCount(): int
    {
        return $this->getRepository()->getAllCount(null, $this->contextService->getHome());
    }
}
