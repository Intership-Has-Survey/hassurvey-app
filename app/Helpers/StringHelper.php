<?php

namespace App\Helpers;

class StringHelper
{


    public static function htmlToTextWithNewlines($html)
    {
        $text = preg_replace('/<br\s*\/?>/i', "\n", $html);
        $text = preg_replace('/<\/p>/i', "\n", $text);
        $text = preg_replace('/<p>/i', "", $text);

        // li
        $text = preg_replace('/<li>/i', "- ", $text);
        $text = preg_replace('/<\/li>/i', "\n", $text);

        // izinkan strong & b
        return trim(strip_tags($text, '<strong><b>'));
    }
}
