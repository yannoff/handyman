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

namespace Yannoff\Handy\Exception;

use Yannoff\Component\Console\Exception\RuntimeException;

/**
 * Thrown by the Kernel::create() method when an unknown kernel class is queried
 */
class InvalidKernelClassException extends RuntimeException
{
    public function __construct(string $kernelClass)
    {
        $message = sprintf('class "%s" not found', $kernelClass);
        parent::__construct($message);
    }

    /**
     * String cast-type formatter for InvalidKernelClassExceptions
     *
     * @return string
     */
    public function __toString()
    {
        return sprintf('Error: %s (code: %s)', $this->message, $this->code);
    }
}
