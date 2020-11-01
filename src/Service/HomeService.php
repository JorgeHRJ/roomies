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

        $hash = substr(sha1(md5($home->getSlug())), 0, Home::HASH_LENGTH);
        $home->setHash($hash);

        return parent::create($home);
    }

    /**
     * @param Home $home
     * @param User $user
     */
    public function addUser(Home $home, User $user): void
    {
        $home->addUser($user);

        $this->entityManager->flush();
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
     * @param string $hash
     * @return Home|null
     */
    public function getByHash(string $hash): ?Home
    {
        return $this->getRepository()->findOneBy(['hash' => $hash]);
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

    /**
     * @return HomeRepository
     */
    public function getRepository(): BaseRepository
    {
        return $this->repository;
    }
}
