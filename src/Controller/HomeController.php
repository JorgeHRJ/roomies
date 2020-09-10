<?php

namespace App\Controller;

use App\Library\Controller\BaseController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/", name="home_")
 */
class HomeController extends BaseController
{
    /**
     * @Route("/", name="index")
     *
     * @return Response
     */
    public function index(): Response
    {
        return $this->render('home/index.html.twig', []);
    }
}
