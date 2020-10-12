<?php

namespace App\Controller\App;

use App\Library\Controller\BaseController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/", name="index_")
 */
class IndexController extends BaseController
{
    /**
     * @Route("/", name="landing")
     *
     * @return Response
     */
    public function landing(): Response
    {
        return $this->render('app/index/landing.html.twig', []);
    }

    /**
     * @Route("/dashboard", name="dashboard")
     *
     * @return Response
     */
    public function dashboard(): Response
    {
        return $this->render('app/index/dashboard.html.twig', []);
    }
}
