<?php

namespace App\Twig;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Yaml\Yaml;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class LayoutExtension extends AbstractExtension
{
    /** @var RequestStack */
    private $requestStack;

    /** @var Environment */
    private $templating;

    /** @var RouterInterface */
    private $router;

    /** @var array */
    private $config;

    public function __construct(
        string $projectDir,
        RequestStack $requestStack,
        Environment $templating,
        RouterInterface $router
    ) {
        $this->requestStack = $requestStack;
        $this->templating = $templating;
        $this->router = $router;

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
        $base = $routeParts[0];
        $baseConfig = $this->config[$base];
        unset($routeParts[0]);

        try {
            $items = [];
            $title = '';
            $temp = ['path' => $base, 'config' => $baseConfig];
            foreach ($routeParts as $part) {
                $partConfig = $temp['config'][$part];

                $itemName = $partConfig['name'];
                $itemPath = null;

                if (isset($partConfig['child'])) {
                    $newTempPath = sprintf('%s_%s', $temp['path'], $partConfig['path']);
                    $itemPath = $this->router->generate($newTempPath, $routeParams);
                    $temp = ['path' => $newTempPath, 'config' => $partConfig];
                }

                $items[] = ['name' => $itemName, 'path' => $itemPath];
                $title = $partConfig['title'];
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
}
