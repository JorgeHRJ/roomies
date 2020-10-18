<?php

namespace App\Service;

use App\Entity\Home;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class ContextService
{
    const LANDING_CONTEXT = 0;
    const APP_CONTEXT = 1;

    /** @var ?Home */
    private $home;

    /** @var int */
    private $context;

    /** @var HomeService */
    private $homeService;

    /** @var SessionInterface */
    private $session;

    public function __construct(HomeService $homeService, SessionInterface $session)
    {
        $this->homeService = $homeService;
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

        $home = $this->homeService->get($homeId);
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