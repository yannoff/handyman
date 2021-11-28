<?php

namespace Yannoff\Handy\Command;

use Yannoff\Handy\Event\Dispatcher;

trait EventAware
{
    public function addListener(string $eventName, callable $callback)
    {
        Dispatcher::bind($eventName, $callback);
    }

    public function dispatch(string $eventName, $data)
    {
        Dispatcher::trigger($eventName, $data);
    }
}