<?php

namespace App\Controller\App;

use App\Entity\Home;
use App\Form\HomeType;
use App\Library\Controller\BaseController;
use App\Service\ContextService;
use App\Service\HomeService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route({
 *     "es": "/hogar",
 *     "en": "/home"
 * }, name="home_")
 */
class HomeController extends BaseController
{
    /** @var HomeService */
    private $homeService;

    /** @var ContextService */
    private $contextService;

    /** @var TranslatorInterface */
    private $translator;

    public function __construct(
        HomeService $homeService,
        ContextService $contextService,
        TranslatorInterface $translator
    ) {
        $this->homeService = $homeService;
        $this->contextService = $contextService;
        $this->translator = $translator;
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

        return $this->render('app/home/dashboard.html.twig', ['home' => $home]);
    }

    /**
     * @Route({
     *     "es": "/entrar/{homeSlug}",
     *     "en": "/enter/{homeSlug}"
     * }, name="enter")
     *
     * @param string $homeSlug
     * @return Response
     */
    public function enter(string $homeSlug): Response
    {
        $home = $this->homeService->getBySlug($homeSlug);
        $this->contextService->setHome($home);

        return $this->redirectToRoute('app_home_dashboard');
    }

    /**
     * @Route({
     *     "es": "/nuevo",
     *     "en": "/new"
     * }, name="new", methods={"GET","POST"})
     *
     * @param Request $request
     * @return Response
     */
    public function new(Request $request): Response
    {
        $home = new Home();
        $form = $this->createForm(HomeType::class, $home);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if (!$form->isValid()) {
                $this->addFlash('app_error', $this->getFormErrorMessagesList($form, true));
                return $this->render('app/home/new.html.twig', ['form' => $form->createView()]);
            }

            try {
                $user = $this->getUserInstance();

                $this->homeService->new($home, $user);

                $this->addFlash(
                    'app_success',
                    $this->translator->trans('home.form.success_message', [], 'home')
                );

                return $this->redirectToRoute('app_index_landing');
            } catch (\Exception $e) {
                $this->addFlash(
                    'app_error',
                    $this->translator->trans('home.form.error_message', [], 'home')
                );
            }
        }

        return $this->render('app/home/new.html.twig', ['form' => $form->createView()]);
    }
}
