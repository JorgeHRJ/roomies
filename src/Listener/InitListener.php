<?php

namespace App\Listener;

use App\Entity\Home;
use App\Service\ContextService;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Routing\RouterInterface;

class InitListener
{
    /** @var ContextService */
    private $contextService;

    /** @var RouterInterface */
    private $router;

    public function __construct(ContextService $contextService, RouterInterface $router)
    {
        $this->contextService = $contextService;
        $this->router = $router;
    }

    public function onRequest(RequestEvent $event): void
    {
        if (!$event->isMasterRequest()) {
            return;
        }
        $request = $event->getRequest();
        $path = $request->getPathInfo();
        $match = $this->router->match($path);

        $context = ContextService::LANDING_CONTEXT;
        if (strpos($path, '/app') === 0) {
            $context = ContextService::APP_CONTEXT;
        }
        $this->contextService->setContext($context);

        $home = $this->contextService->getHome();
        if (!$home instanceof Home
            && $context === ContextService::APP_CONTEXT
            && !in_array($match['_route'], $this->getExcludedRoutes())) {
            $url = $this->router->generate('app_index_landing');
            $response = new RedirectResponse($url);
            $event->setResponse($response);
        }
    }

    private function getExcludedRoutes(): array
    {
        return ['app_home_enter', 'app_index_landing', 'app_home_new', 'app_home_join', 'app_home_join_confirm'];
    }
}
