<?php

if (!function_exists('snakeToSentenceCase')) {
    function snakeToSentenceCase(string $string): string
    {
        return ucfirst(str_replace('_', ' ', strtolower($string)));
    }
}
