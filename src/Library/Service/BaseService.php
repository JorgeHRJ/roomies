<?php

namespace App\Library\Service;

use App\Library\Repository\BaseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Psr\Log\LoggerInterface;

abstract class BaseService
{
    /** @var EntityManagerInterface */
    protected $entityManager;

    /** @var LoggerInterface */
    protected $logger;

    public function __construct(EntityManagerInterface $entityManager, LoggerInterface $logger)
    {
        $this->entityManager = $entityManager;
        $this->logger = $logger;
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
}
