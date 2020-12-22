<?php

namespace App\Twig\Tag\PageSetup;

use Twig\Node\Node;
use Twig\Compiler;
use Twig\Node\Expression\AbstractExpression;

class PageSetupNode extends Node
{
    public function __construct(AbstractExpression $value, string $name, int $line, string $tag = null)
    {
        parent::__construct(['value' => $value], ['name' => $name], $line, $tag);
    }

    public function compile(Compiler $compiler)
    {
        $compiler
            ->raw('$this->env->getExtension(\'App\Twig\Extension\PageSetupExtension\')->configure(')
            ->subcompile($this->getNode('value'))
            ->raw(');');
    }
}
