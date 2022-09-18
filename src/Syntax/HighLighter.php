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

use PHP_Parallel_Lint\PhpConsoleHighlighter\Highlighter as BaseHighlighter;

class HighLighter extends BaseHighlighter
{
    public static function create(): HighLighter
    {
        return new static(new Colors());
    }

    public function render(string $code): string
    {
        $pretty = $this->getWholeFile("<?php\n$code");

        // Remove the php opening tag line
        $lines = explode(PHP_EOL, $pretty);
        array_shift($lines);

        return implode("\n", $lines);
    }
}
