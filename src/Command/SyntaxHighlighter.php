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

use Yannoff\Handy\Syntax\HighLighter;

/**
 *
 */
trait SyntaxHighlighter
{
    /**
     * @var Highlighter
     */
    protected $highlighter;

    /**
     * @return Highlighter
     */
    protected function getHighlighter(): HighLighter
    {
        if (null == $this->highlighter) {
            $this->highlighter = HighLighter::create();
        }

        return $this->highlighter;
    }

    /**
     * Return a syntax highlighted version of the PHP code
     *
     * @param string $code The input PHP code
     *
     * @return string
     */
    protected function highlight(string $code): string
    {
        return $this->getHighlighter()->render($code);
    }
}
