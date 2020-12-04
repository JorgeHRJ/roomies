<?php

namespace App\Controller\Landing;

use App\Entity\User;
use App\Form\RegisterUserType;
use App\Library\Controller\BaseController;
use App\Library\Factory\Mail\RegistrationMail;
use App\Service\MailerService;
use App\Service\UserService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class SecurityController extends BaseController
{
    /** @var UserService */
    private $userService;

    /** @var MailerService */
    private $mailerService;

    /** @var AuthorizationCheckerInterface */
    private $authChecker;

    /** @var TranslatorInterface */
    private $translator;

    public function __construct(
        UserService $userService,
        MailerService $mailerService,
        AuthorizationCheckerInterface $authChecker,
        TranslatorInterface $translator
    ) {
        $this->userService = $userService;
        $this->mailerService = $mailerService;
        $this->authChecker = $authChecker;
        $this->translator = $translator;
    }

    /**
     * @Route("/login", name="security_login")
     *
     * @param AuthenticationUtils $authenticationUtils
     *
     * @return Response
     */
    public function login(AuthenticationUtils $authenticationUtils)
    {
        if ($this->authChecker->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute('app_index_landing');
        }

        $error = $authenticationUtils->getLastAuthenticationError();
        if ($error instanceof \Exception) {
            $error = new BadCredentialsException(
                $this->translator->trans('security.login.general_error', [], 'security')
            );
        }

        return $this->render('landing/security/login.html.twig', [
            'last_username' => $authenticationUtils->getLastUsername(),
            'error' => $error,
        ]);
    }

    /**
     * @Route({
     *     "es": "/registro",
     *     "en": "/register"
     * }, name="security_register", methods={"GET", "POST"})
     *
     * @param Request $request
     *
     * @return Response
     */
    public function register(Request $request)
    {
        if ($this->authChecker->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute('app_index_landing');
        }

        $user = new User();

        $form = $this->createForm(RegisterUserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if (!$form->isValid()) {
                $this->addFlash('app_error', $this->getFormErrorMessagesList($form));

                return $this->render('landing/security/register.html.twig', ['form' => $form->createView()]);
            }

            try {
                $this->userService->create($form->getData());

                $mail = new RegistrationMail();
                $mail->prepare($user->getEmail(), ['name' => $user->getName(), 'uuid' => $user->getUuid()]);
                $this->mailerService->send($mail);

                $this->addFlash(
                    'app_success',
                    $this->translator->trans('security.register.success_register', [], 'security')
                );

                return $this->redirect($this->generateUrl('security_register_ok'));
            } catch (\Exception $e) {
                $this->addFlash(
                    'app_error',
                    $this->translator->trans('security.register.error', [], 'security')
                );
            }
        }

        return $this->render('landing/security/register.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route({
     *     "es": "/registro/ok",
     *     "en": "/register/ok"
     * }, name="security_register_ok")
     *
     * @return Response
     */
    public function registerOk()
    {
        return $this->render('landing/security/register_successful.html.twig');
    }

    /**
     * @Route({
     *     "es": "/registro/confirmacion/{uuid}",
     *     "en": "/register/confirm/{uuid}"
     * }, name="security_register_confirm", requirements={"uuid"="[0-9a-zA-Z\-\_]+"})
     *
     * @param string $uuid
     *
     * @return Response
     * @throws \Exception
     */
    public function confirm(string $uuid)
    {
        $user = $this->userService->getByUuid($uuid);
        if (!$user instanceof User) {
            throw new NotFoundHttpException();
        }

        $this->userService->enable($user);

        $this->addFlash(
            'app_success',
            $this->translator->trans('security.register.success_confirm', [], 'security')
        );

        return $this->redirect($this->generateUrl('security_login'));
    }
}
