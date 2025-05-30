<?php

if (!function_exists('snakeToSentenceCase')) {
    function snakeToSentenceCase(string $string): string
    {
        return ucfirst(str_replace('_', ' ', strtolower($string)));
    }
}

if (!function_exists('snakeToPascalCase')) {
    function snakeToPascalCase(string $input): string
    {
        return str_replace(' ', '', ucwords(str_replace('_', ' ', $input)));
    }
}
