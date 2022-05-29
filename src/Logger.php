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

namespace Yannoff\Handy;

use Yannoff\Component\Console\IO\Output\Formatter;
use Yannoff\Component\Console\IO\Output\FormatterFactory;

/**
 * Basic logger class
 */
class Logger
{
    /**
     * The Logger singleton instance
     *
     * @var Logger
     */
    protected static $instance;

    /**
     * The console output formatter instance
     *
     * @var Formatter
     */
    protected $formatter;

    /**
     * Whether debugging is turned on or not
     *
     * @var bool
     */
    protected $debug;

    /**
     * @param Formatter $formatter
     * @param bool      $debug
     */
    public function __construct(Formatter $formatter, bool $debug = false)
    {
        $this->debug = $debug;
        $this->formatter = $formatter;
    }

    /**
     * Facade method for message printing
     *
     * @param string $message
     *
     * @return void
     */
    public static function debug(string $message)
    {
        self::get()->write($message);
    }

    /**
     * Logger factory method
     *
     * @return Logger
     */
    protected static function create(): Logger
    {
        $active = Config::get('verbose', false);
        $formatter = FormatterFactory::create();

        return new static($formatter, $active);
    }

    /**
     * Getter & initializer for the Logger instance
     *
     * @return Logger
     */
    protected static function get(): Logger
    {
        if (null === self::$instance) {
            self::$instance = self::create();
        }

        return self::$instance;
    }

    /**
     * Print the given message if debugging is turned on
     *
     * @param string $message
     *
     * @return void
     */
    protected function write(string $message)
    {
        if (! $this->debug) {
            return;
        }

        $formatted = $this->formatter->format($message, null);

        printf("[handyman] %s\n", $formatted);
    }
}
