<?php

/**
 * This file is part of the yannoff/handyman library
 *
 * Copyright (c) Yannoff (https://github.com/yannoff)
 *
 * For the full copyright and license information,
 * please view the LICENSE file bundled with this
 * source code.
 */

namespace Yannoff\Handy\Command;

use PHP_Parallel_Lint\PhpConsoleColor\ConsoleColor;
use PHP_Parallel_Lint\PhpConsoleHighlighter\Highlighter;

/**
 *
 */
trait SyntaxHighlighter
{
    /**
     * Replace initial hex colorset by their named-color replacement
     */
    protected function initializeHighlighter()
    {
        $map = [
            'comment' => 'grey',
            'default' => 'white',
            'html' => 'yellow',
            'keyword' => 'magenta',
            'string' => 'cyan',
        ];

        foreach ($map as $type => $color) {
            ini_set("highlight.$type", $color);
        }
    }

    /**
     * Return a syntax highlighted version of the PHP code
     *
     * @fixme HTML to console tags not working
     *
     * @param string $code The input PHP code
     *
     * @return string
     */
    protected function highlight($code)
    {
        $colors = new ConsoleColor();
        //TODO: $colors->addTheme();
        $syntax = new Highlighter($colors);
        $pretty = $syntax->getWholeFile("<?php\n$code");
        $lines = explode(PHP_EOL, $pretty);
        array_shift($lines);
        return implode("\n", $lines);

        $this->initializeHighlighter();

        $pretty = highlight_string("<?php\n$code", true);

        $html2posix = [
            '&nbsp;' => ' ',
            '&lt;' => '<',
            '&gt;' => '>',
            '&le;' => '<=',
            '&ge;' => '>=',
        ];

        $pretty = preg_replace('#<code><span style="color: ([A-Za-z]*)">$\n\s*(.*)</span>$\n*\s*</code>#m', "<\$1>\$2</\$1>", $pretty);
        //$pretty = preg_replace('#<span style="color: ([A-Za-z]*)">$\n\s*(.*)</span>#m', "<\$1>\$2</\$1>", $pretty);
        //$pretty = htmlspecialchars_decode($pretty);
        $pretty = str_replace('&lt;?<br />', '', $pretty);
        $pretty = str_replace(array_keys($html2posix), array_values($html2posix), $pretty);
        $lines = explode("<br />", $pretty);
        //$lines[0] = str_replace('&lt;?', '', $lines[0]);
        return implode("\n", $lines);
    }
}
