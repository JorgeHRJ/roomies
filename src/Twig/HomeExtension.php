<?php

namespace App\Twig;

use App\Entity\Home;
use App\Entity\User;
use App\Service\ContextService;
use App\Service\HomeService;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Security;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class HomeExtension extends AbstractExtension
{
    /** @var ContextService */
    private $contextService;

    /** @var Security */
    private $security;

    /** @var HomeService */
    private $homeService;

    public function __construct(
        ContextService $contextService,
        HomeService $homeService,
        Security $security
    ) {
        $this->contextService = $contextService;
        $this->homeService = $homeService;
        $this->security = $security;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('get_current_home', [$this, 'getCurrentHome']),
            new TwigFunction('get_homes', [$this, 'getHomes'])
        ];
    }

    /**
     * @return Home
     */
    public function getCurrentHome(): Home
    {
        return $this->contextService->getHome();
    }

    /**
     * @return Home[]
     */
    public function getHomes(): array
    {
        $user = $this->security->getUser();
        if (!$user instanceof User) {
            throw new AccessDeniedException();
        }

        return $this->homeService->getByUser($user);
    }
}
