<?php


namespace App\Service;


class Slugify
{
    public function removeSpecialCharacters($input) : string
    {
        $utf8 = [
            '/[áàâãªäÁÀÂÃÄ]/u' => 'a',
            '/[íìîïÍÌÎÏ]/u' => 'i',
            '/[éèêëÉÈÊË]/u' => 'e',
            '/[óòôõºöÓÒÔÕÖ]/u' => 'o',
            '/[úùûüÚÙÛÜ]/u' => 'u',
            '/çÇ/' => 'c',
            '/ñÑ/' => 'n',
            '/[\'«»!?,.;:]/u' => '',
        ];
        return preg_replace(array_keys($utf8), array_values($utf8), $input);
    }

    public function generate(string $input) : string
    {
        return trim(strtolower(preg_replace(['#[^A-Za-z0-9 -]+#', '#[\s-]+#'], ['', '-'], $this->removeSpecialCharacters($input))));
    }
}
