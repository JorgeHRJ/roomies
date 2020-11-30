<?php

namespace App\Service;

use App\Entity\Home;
use App\Repository\HomeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class ContextService
{
    const LANDING_CONTEXT = 0;
    const APP_CONTEXT = 1;

    /** @var ?Home */
    private $home;

    /** @var int */
    private $context;

    /** @var HomeRepository */
    private $homeRepository;

    /** @var SessionInterface */
    private $session;

    public function __construct(EntityManagerInterface $entityManager, SessionInterface $session)
    {
        $this->homeRepository = $entityManager->getRepository(Home::class);
        $this->session = $session;
    }

    /**
     * @return Home|null
     */
    public function getHome(): ?Home
    {
        if ($this->home instanceof Home) {
            return $this->home;
        }

        $homeId = $this->session->get('home-id', null);
        if ($homeId === null) {
            return null;
        }

        $home = $this->homeRepository->find($homeId);
        if (!$home instanceof Home) {
            return null;
        }

        $this->setHome($home);

        return $home;
    }

    /**
     * @param Home $home
     */
    public function setHome(Home $home): void
    {
        $this->home = $home;
        $this->session->set('home-id', $home->getId());
    }

    public function removeHome(): void
    {
        $this->home = null;
        $this->session->remove('home-id');
    }

    /**
     * @return int
     */
    public function getContext(): int
    {
        return $this->context;
    }

    /**
     * @param int $context
     */
    public function setContext(int $context): void
    {
        $this->context = $context;
    }
}
