<?php

namespace App\Controller\App;

use App\Entity\Home;
use App\Entity\File;
use App\Form\HomeType;
use App\Library\Controller\BaseController;
use App\Service\ContextService;
use App\Service\FileService;
use App\Service\HomeService;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
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

    /** @var FileService */
    private $fileService;

    /** @var TranslatorInterface */
    private $translator;

    public function __construct(
        HomeService $homeService,
        ContextService $contextService,
        FileService $fileService,
        TranslatorInterface $translator
    ) {
        $this->homeService = $homeService;
        $this->contextService = $contextService;
        $this->fileService = $fileService;
        $this->translator = $translator;
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
        $user = $this->getUserInstance();

        if (!$home->getUsers()->contains($user)) {
            throw new AccessDeniedException();
        }

        $this->contextService->setHome($home);

        return $this->redirectToRoute('app_index_dashboard');
    }

    /**
     * @Route({
     *     "es": "/unir/{hash}",
     *     "en": "/join/{hash}"
     * }, name="join", requirements={"hash"="[0-9a-zA-Z\-\_]+"})
     *
     * @param string|null $hash
     * @return Response
     */
    public function join(string $hash = null): Response
    {
        if ($hash !== null) {
            return $this->render('app/home/join.html.twig', ['home' => $this->homeService->getByHash($hash)]);
        }

        return $this->render('app/home/join_no_hash.html.twig');
    }

    /**
     * @Route({
     *     "es": "/unir/{hash}/confirmacion",
     *     "en": "/join/{hash}/confirm"
     * }, name="join_confirm", requirements={"hash"="[0-9a-zA-Z\-\_]+"})
     *
     * @param string $hash
     * @return Response
     */
    public function confirmJoin(string $hash): Response
    {
        $home = $this->homeService->getByHash($hash);
        if (!$home instanceof Home) {
            throw new NotFoundHttpException();
        }

        $user = $this->getUserInstance();
        $this->homeService->addUser($home, $user);

        return $this->redirectToRoute('app_home_enter', ['homeSlug' => $home->getSlug()]);
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

                $home = $this->homeService->new($home, $user);
                $this->contextService->setHome($home);

                $avatarFile = $form->get('avatar')->getData();
                if ($avatarFile instanceof UploadedFile) {
                    $this->fileService->handleUpload($avatarFile, File::HOME_AVATAR_ORIGIN);
                }

                $this->addFlash(
                    'app_success',
                    $this->translator->trans('home.form.new.success_message', [], 'home')
                );

                return $this->redirectToRoute('app_index_landing');
            } catch (\Exception $e) {
                $this->contextService->removeHome();
                $this->addFlash(
                    'app_error',
                    $this->translator->trans('home.form.new.error_message', [], 'home')
                );
            }
        }

        return $this->render('app/home/new.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route({
     *     "es": "/editar",
     *     "en": "/edit"
     * }, name="edit", methods={"GET","POST"})
     *
     * @param Request $request
     * @return Response
     */
    public function edit(Request $request): Response
    {
        $home = $this->contextService->getHome();

        $form = $this->createForm(HomeType::class, $home);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if (!$form->isValid()) {
                $this->addFlash('app_error', $this->getFormErrorMessagesList($form, true));
                return $this->render('app/home/edit.html.twig', ['form' => $form->createView(), 'home' => $home]);
            }

            try {
                /** @var Home $home */
                $home = $form->getData();

                $avatarFile = $form->get('avatar')->getData();
                if ($avatarFile instanceof UploadedFile) {
                    $previousAvatar = $this->fileService->getAvatarByHome($home);
                    if ($previousAvatar instanceof File) {
                        $home->removeFile($previousAvatar);
                        $this->fileService->remove($previousAvatar);
                    }

                    $avatar = $this->fileService->handleUpload($avatarFile, File::HOME_AVATAR_ORIGIN);
                    $home->addFile($avatar);
                }

                $this->homeService->edit($home);

                $this->addFlash(
                    'app_success',
                    $this->translator->trans('home.form.edit.success_message', [], 'home')
                );
            } catch (\Exception $exception) {
                $this->addFlash(
                    'app_error',
                    $this->translator->trans('home.form.edit.error_message', [], 'home')
                );
            }
        }

        return $this->render('app/home/edit.html.twig', ['form' => $form->createView(), 'home' => $home]);
    }
}
