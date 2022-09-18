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

namespace Yannoff\Handy\Syntax;

use PHP_Parallel_Lint\PhpConsoleColor\ConsoleColor;
use PHP_Parallel_Lint\PhpConsoleHighlighter\Highlighter;

class Colors extends ConsoleColor
{
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

    public function __construct()
    {
        parent::__construct();
        $this->setThemes($this->theme);
    }
}