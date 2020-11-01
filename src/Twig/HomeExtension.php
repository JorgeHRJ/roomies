<?php

namespace App\Twig;

use App\Entity\File;
use App\Entity\Home;
use App\Entity\User;
use App\Service\ContextService;
use App\Service\FileService;
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

    /** @var FileService */
    private $fileService;

    public function __construct(
        ContextService $contextService,
        HomeService $homeService,
        FileService $fileService,
        Security $security
    ) {
        $this->contextService = $contextService;
        $this->homeService = $homeService;
        $this->fileService = $fileService;
        $this->security = $security;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('get_current_home', [$this, 'getCurrentHome']),
            new TwigFunction('get_homes', [$this, 'getHomes']),
            new TwigFunction('get_avatar', [$this, 'getAvatar']),
            new TwigFunction('get_home_avatar', [$this, 'getHomeAvatar'])
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

    /**
     * @return File|null
     */
    public function getAvatar(): ?File
    {
        return $this->fileService->getAvatarByHome($this->contextService->getHome());
    }

    /**
     * @param Home $home
     * @return File|null
     */
    public function getHomeAvatar(Home $home): ?File
    {
        return $this->fileService->getAvatarByHome($home);
    }
}
