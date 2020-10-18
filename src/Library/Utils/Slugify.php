<?php

namespace App\Library\Utils;

class Slugify
{
    /**
     * Convert a text into a slug formatted text
     *
     * @param string $text
     * @return string
     */
    public static function slugify(string $text): string
    {
        $text = htmlentities($text, ENT_QUOTES, 'UTF-8');
        $text = preg_replace(
            '~&([a-z]{1,2})(acute|cedil|circ|grave|lig|orn|ring|slash|th|tilde|uml);~i',
            '$1',
            $text
        );
        $text = preg_replace('~[^0-9a-z]+~i', '-', $text);
        $text = trim($text, ' ');
        $text = strtolower($text);

        return $text;
    }
}
