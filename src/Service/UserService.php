<?php

namespace App\Service;

use App\Entity\User;
use App\Library\Repository\BaseRepository;
use App\Library\Service\BaseService;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class UserService extends BaseService
{
    /** @var UserRepository */
    private $repository;

    public function __construct(
        EntityManagerInterface $entityManager,
        LoggerInterface $logger
    ) {
        parent::__construct($entityManager, $logger);
        $this->repository = $entityManager->getRepository(User::class);
    }

    /**
     * @param User $entity
     * @return User
     * @throws \Exception
     */
    public function create($entity): User
    {
        $entity->setStatus(User::ENABLED_STATUS);
        $entity->setRole(User::ROLE_USER);

        return parent::create($entity);
    }

    public function getSortFields(): array
    {
        return [];
    }

    /**
     * @return UserRepository
     */
    public function getRepository(): BaseRepository
    {
        return $this->repository;
    }
}
