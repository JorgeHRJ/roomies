<?php

namespace App\Twig\Extension;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use App\Twig\Tag\PageSetup\PageSetupTokenParser;

class PageSetupExtension extends AbstractExtension
{
    const BODY_DATA_ATTR_ALLOWED = ['controller', 'page'];

    /** @var string */
    private $title;

    /** @var string */
    private $header;

    /** @var string */
    private $subheader;

    /** @var array */
    private $breadcrumbs;

    /** @var string */
    private $bodyClass;

    /** @var array */
    private $bodyAttr;

    public function __construct()
    {
        $this->title = '';
        $this->header = '';
        $this->subheader = '';
        $this->breadcrumbs = [];
        $this->bodyClass = 'bg-soft';
        $this->bodyAttr = [];
    }

    public function getTokenParsers()
    {
        return [
            new PageSetupTokenParser(),
        ];
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('get_title', [$this, 'getTitle']),
            new TwigFunction('get_header', [$this, 'getHeader']),
            new TwigFunction('get_subheader', [$this, 'getSubheader']),
            new TwigFunction('get_breadcrumbs', [$this, 'getBreadcrumbs']),
            new TwigFunction('get_body_class', [$this, 'getBodyClass']),
            new TwigFunction('get_body_attr', [$this, 'getBodyAttr']),
        ];
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getHeader(): string
    {
        return $this->header;
    }

    /**
     * @return string
     */
    public function getSubheader(): string
    {
        return $this->subheader;
    }

    /**
     * @return array
     */
    public function getBreadcrumbs(): array
    {
        return $this->breadcrumbs;
    }

    /**
     * @return string
     */
    public function getBodyClass(): string
    {
        return $this->bodyClass;
    }

    /**
     * @return string
     */
    public function getBodyAttr(): string
    {
        $output = '';
        foreach (self::BODY_DATA_ATTR_ALLOWED as $attr) {
            if (isset($this->bodyAttr[$attr])) {
                $output = sprintf('%s data-%s=%s', $output, $attr, $this->bodyAttr[$attr]);
            }
        }

        return $output;
    }

    /**
     * @param array $config
     */
    public function configure(array $config): void
    {
        if (isset($config['title'])) {
            $this->title = $config['title'];
        }

        if (isset($config['header'])) {
            $this->header = $config['header'];
        }

        if (isset($config['subheader'])) {
            $this->subheader = $config['subheader'];
        }

        if (isset($config['breadcrumbs'])) {
            $this->breadcrumbs = $config['breadcrumbs'];
        }

        if (isset($config['body'])) {
            if (isset($config['body']['class'])) {
                $this->bodyClass = $config['body']['class'];
            }

            if (isset($config['body']['attr'])) {
                $this->bodyAttr = $config['body']['attr'];
            }
        }
    }
}
