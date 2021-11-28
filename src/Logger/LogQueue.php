<?php

namespace Yannoff\Handy\Logger;

/**
 * Log messages stack
 */
class LogQueue
{
    /**
     * @var string[]
     */
    public static $messages = [];

    /**
     * @param string $message
     */
    public static function add(string $message)
    {
        self::$messages[] = $message;
    }

    /**
     * @return string[]
     */
    public static function all(): array
    {
        return self::$messages;
    }

    /**
     * Clear the stack
     */
    public static function flush()
    {
        self::$messages = [];
    }
}