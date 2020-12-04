<?php

namespace App\Service;

use App\Entity\User;
use App\Library\Repository\BaseRepository;
use App\Library\Service\BaseService;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Uid\Uuid;

class UserService extends BaseService
{
    /** @var UserRepository */
    private $repository;

    public function __construct(
        EntityManagerInterface $entityManager,
        LoggerInterface $logger,
        ContextService $contextService
    ) {
        parent::__construct($entityManager, $logger, $contextService);
        $this->repository = $entityManager->getRepository(User::class);
    }

    /**
     * @param User $entity
     * @return User
     * @throws \Exception
     */
    public function create($entity): User
    {
        $entity->setStatus(User::DISABLED_STATUS);
        $entity->setRole(User::ROLE_USER);
        $entity->setUuid(Uuid::v4());

        return parent::create($entity);
    }

    /**
     * @param User $user
     * @throws \Exception
     */
    public function enable(User $user): void
    {
        $user->setStatus(User::ENABLED_STATUS);
        $this->edit($user);
    }

    /**
     * @param string $uuid
     * @return User|null
     */
    public function getByUuid(string $uuid): ?User
    {
        return $this->repository->findOneBy(['uuid' => $uuid, 'status' => User::DISABLED_STATUS]);
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
