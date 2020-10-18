<?php

namespace App\Service;

use App\Entity\Home;
use App\Entity\User;
use App\Library\Repository\BaseRepository;
use App\Library\Service\BaseService;
use App\Library\Utils\Slugify;
use App\Repository\HomeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class HomeService extends BaseService
{
    /** @var HomeRepository */
    private $repository;

    public function __construct(EntityManagerInterface $entityManager, LoggerInterface $logger)
    {
        parent::__construct($entityManager, $logger);
        $this->repository = $entityManager->getRepository(Home::class);
    }

    /**
     * @param Home $home
     * @param User $user
     *
     * @return Home
     *
     * @throws \Exception
     */
    public function new(Home $home, User $user): Home
    {
        $home->addUser($user);
        $home->setSlug(Slugify::slugify($home->getName()));

        return parent::create($home);
    }

    /**
     * @param User $user
     * @return array
     */
    public function getByUser(User $user): array
    {
        return $this->getRepository()->findByUser($user);
    }

    /**
     * @param string $slug
     * @return Home|null
     */
    public function getBySlug(string $slug): ?Home
    {
        return $this->getRepository()->findOneBy(['slug' => $slug]);
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
