<?php

namespace App\Controller\App;

use App\Library\Controller\BaseController;
use App\Service\ContextService;
use App\Service\HomeService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/", name="index_")
 */
class IndexController extends BaseController
{
    /** @var HomeService */
    private $homeService;

    /** @var ContextService */
    private $contextService;

    public function __construct(HomeService $homeService, ContextService $contextService)
    {
        $this->homeService = $homeService;
        $this->contextService = $contextService;
    }

    /**
     * @Route("/", name="landing")
     *
     * @return Response
     */
    public function landing(): Response
    {
        $user = $this->getUserInstance();

        $homes = $this->homeService->getByUser($user);
        return $this->render('app/index/landing.html.twig', ['homes' => $homes]);
    }

    /**
     * @Route({
     *     "es": "/panel",
     *     "en": "/dashboard"
     * }, name="dashboard")
     *
     * @return Response
     */
    public function dashboard(): Response
    {
        $home = $this->contextService->getHome();

        return $this->render('app/index/dashboard.html.twig', ['home' => $home]);
    }
}
