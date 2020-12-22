<?php

namespace App\Twig\Tag\PageSetup;

use Twig\Error\SyntaxError;
use Twig\Node\Node;
use Twig\TokenParser\AbstractTokenParser;
use Twig\Token;

class PageSetupTokenParser extends AbstractTokenParser
{
    /**
     * Parses a token and returns a node.
     *
     * @param Token $token
     * @return Node
     *
     * @throws SyntaxError
     */
    public function parse(Token $token)
    {
        $stream = $this->parser->getStream();

        $value = $this->parser->getExpressionParser()->parseExpression();
        $stream->expect(Token::BLOCK_END_TYPE);

        return new PageSetupNode($value, $this->getTag(), $token->getLine(), $this->getTag());
    }

    /**
     * Gets the tag name associated with this token parser.
     *
     * @return string
     */
    public function getTag()
    {
        return 'page_setup';
    }
}
