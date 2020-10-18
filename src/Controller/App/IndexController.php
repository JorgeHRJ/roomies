<?php

namespace App\Controller\App;

use App\Library\Controller\BaseController;
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

    public function __construct(HomeService $homeService)
    {
        $this->homeService = $homeService;
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
}
