<?php

namespace Yannoff\Handy\Event;

class Dispatcher
{
    protected static $listeners = [];

    public static function bind(string $eventName, callable $callback)
    {
        self::$listeners[$eventName][] = $callback;
    }

    public static function trigger(string $eventName, $data)
    {
        $stack = self::$listeners[$eventName];
        foreach ($stack as $callback) {
            call_user_func($callback, $data);
        }
    }
}
