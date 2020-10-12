<?php

namespace App\Controller\Landing;

use App\Entity\User;
use App\Form\RegisterUserType;
use App\Library\Controller\BaseController;
use App\Service\UserService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class SecurityController extends BaseController
{
    /** @var UserService */
    private $userService;

    /** @var AuthorizationCheckerInterface */
    private $authChecker;

    /** @var TranslatorInterface */
    private $translator;

    public function __construct(
        UserService $userService,
        AuthorizationCheckerInterface $authChecker,
        TranslatorInterface $translator
    ) {
        $this->userService = $userService;
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
     * @Route("/register", name="security_register", methods={"GET", "POST"})
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

                $this->addFlash(
                    'app_success',
                    $this->translator->trans('security.register.success', [], 'security')
                );

                return $this->redirect($this->generateUrl('security_login'));
            } catch (\Exception $e) {
                $this->addFlash(
                    'app_error',
                    $this->translator->trans('security.register.error', [], 'security')
                );
            }
        }

        return $this->render('landing/security/register.html.twig', ['form' => $form->createView()]);
    }
}
