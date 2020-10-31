<?php

namespace App\Twig;

use App\Entity\Home;
use App\Entity\User;
use App\Service\ContextService;
use App\Service\HomeService;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Yaml\Yaml;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class LayoutExtension extends AbstractExtension
{
    const TRANSLATION_DOMAIN = 'messages';
    const TRANSLATION_BREADCRUMB_BASE = 'breadcrumb.items';

    /** @var RequestStack */
    private $requestStack;

    /** @var Environment */
    private $templating;

    /** @var RouterInterface */
    private $router;

    /** @var array */
    private $config;

    /** @var TranslatorInterface */
    private $translator;

    public function __construct(
        string $projectDir,
        RequestStack $requestStack,
        Environment $templating,
        RouterInterface $router,
        TranslatorInterface $translator
    ) {
        $this->requestStack = $requestStack;
        $this->templating = $templating;
        $this->router = $router;
        $this->translator = $translator;

        $configFile = $projectDir . '/config/app/breadcrumb.yaml';
        $this->config = Yaml::parse(file_get_contents($configFile))['config'];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('get_breadcrumb', [$this, 'getBreadcrumb']),
        ];
    }

    public function getBreadcrumb(): string
    {
        $request = $this->requestStack->getCurrentRequest();
        $route = $request->attributes->get('_route');
        $routeParams = $request->attributes->get('_route_params');

        $routeParts = explode('_', $route);
        if ($routeParts[0] === 'app') {
            unset($routeParts[0]);
            $routeParts = array_values($routeParts);
        }
        $base = $routeParts[0];
        $baseConfig = $this->config[$base];
        unset($routeParts[0]);

        try {
            $items = [];
            $title = '';
            $temp = ['path' => $base, 'config' => $baseConfig];
            foreach ($routeParts as $part) {
                $partConfig = $temp['config'][$part];

                $itemName = $this->getTranslation($partConfig['name']);
                $itemPath = null;

                if (isset($partConfig['child'])) {
                    $newTempPath = sprintf('%s_%s', $temp['path'], $partConfig['path']);
                    $itemPath = $this->router->generate($newTempPath, $routeParams);
                    $temp = ['path' => $newTempPath, 'config' => $partConfig];
                }

                $items[] = ['name' => $itemName, 'path' => $itemPath];
                $title = $this->getTranslation($partConfig['title']);
            }
        } catch (\Exception $e) {
            return '';
        }

        try {
            return $this->templating->render('components/breadcrumb.html.twig', [
                'title' => $title,
                'items' => $items
            ]);
        } catch (LoaderError $e) {
            return '';
        } catch (RuntimeError $e) {
            return '';
        } catch (SyntaxError $e) {
            return '';
        }
    }

    /**
     * @param string $item
     * @return string
     */
    private function getTranslation(string $item): string
    {
        return $this->translator->trans(
            sprintf('%s.%s', self::TRANSLATION_BREADCRUMB_BASE, $item),
            [],
            self::TRANSLATION_DOMAIN
        );
    }
}
