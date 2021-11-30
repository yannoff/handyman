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
     * @var Highlighter
     */
    protected $highlighter;

    /** @var array */
    protected $theme = [
        Highlighter::TOKEN_STRING => 'green',
        Highlighter::TOKEN_COMMENT => 'dark_gray',
        Highlighter::TOKEN_KEYWORD => 'magenta',
        Highlighter::TOKEN_DEFAULT => 'default',
        Highlighter::TOKEN_HTML => 'cyan',

        Highlighter::ACTUAL_LINE_MARK  => 'red',
        Highlighter::LINE_NUMBER => 'dark_gray',
    ];

    /**
     * @return Highlighter
     */
    protected function getHighlighter()
    {
        if (null == $this->highlighter) {
            $this->initializeHighlighter();
        }

        return $this->highlighter;
    }
    /**
     * Initialize the highlighter instance & populate the highlighter property
     */
    protected function initializeHighlighter()
    {
        $colors = new ConsoleColor();
        $colors->setThemes($this->theme);
        $this->highlighter = new Highlighter($colors);
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
        $pretty = $this->getHighlighter()->getWholeFile("<?php\n$code");

        // Remove the php opening tag line
        $lines = explode(PHP_EOL, $pretty);
        array_shift($lines);

        return implode("\n", $lines);
    }
}
