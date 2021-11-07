<?php

/*
|--------------------------------------------------------------------------
| Other Framework Tools
|--------------------------------------------------------------------------
|
| Useful tools which haven't been included in helper and facades.
|
| + CSS
|   - function  loadCSS
|
| + JavaScript
|   - function  loadJS
|
*/

if (!function_exists('loadCSS'))
{
    /**
     * Output to HTML the compressed contents of assigned CSS file.
     *
     * @param  string  $file  Path of the CSS file
     * @return void
     */
    function loadCSS(string $file): void
    {
        $text = file_get_contents($file);
        $text = preg_replace('/[\r\n]/', '', $text);                     // Remove all line breaks
        $text = preg_replace('/ {2,}/', ' ', $text);                     // Keep only 1 blank while there are more blanks
        $text = preg_replace('/ *{ */', '{', $text);                     // Remove blanks in front of or right behind a left brace
        $text = preg_replace('/: */', ':', $text);                       // Remove blanks **right behind** a colon
        $text = preg_replace('/ *; */', ';', $text);                     // Remove blanks in front of or right behind a semicolon
        $text = preg_replace('/ *, */', ',', $text);                     // Remove blanks in front of or right behind a comma
        $text = preg_replace('/ *> */', '>', $text);                     // Remove blanks in front of or right behind a great than symbol (element > element selector)
        $text = preg_replace('/ *\/\*(?!\*\/).+?\*\/ */', '', $text);    // Remove the comments
        echo $text;
    }
}

if (!function_exists('loadJS'))
{
    /**
     * Output to HTML the compressed contents of assigned JavaScript file.
     *
     * **All blank to be kept must be coded as raw character (`\x20`) or HTML entity (`&nbsp;`).**
     *
     * @param  string  $file  Path of the JavaScript file
     * @return void
     */
    function loadJS(string $file): void
    {
        $text = file_get_contents($file);
        $text = preg_replace('/\/\/.*/', '', $text);                     // Remove single line comments before the line breaks
        $text = preg_replace('/[\r\n]/', '', $text);                     // Remove all line breaks
        $text = preg_replace('/ {2,}/', ' ', $text);                     // Keep only 1 blank while there are more blanks
        $text = preg_replace('/ *{ */', '{', $text);                     // Remove blanks in front of or right behind a left brace
        $text = preg_replace('/ *} */', '}', $text);                     // Remove blanks in front of or right behind a right brace
        $text = preg_replace('/ *= */', '=', $text);                     // Remove blanks in front of or right behind a equal sign
        $text = preg_replace('/ *([\+\-\*\/\^&<>]) */', '$1', $text);    // Remove blanks in front of or right behind a math operator
        $text = preg_replace('/: */', ':', $text);                       // Remove blanks **right behind** a colon
        $text = preg_replace('/ *; */', ';', $text);                     // Remove blanks in front of or right behind a semicolon
        $text = preg_replace('/ *, */', ',', $text);                     // Remove blanks in front of or right behind a comma
        $text = preg_replace('/ *\/\*(?!\*\/).+?\*\/ */', '', $text);    // Remove block comments
        echo $text;
    }
}
