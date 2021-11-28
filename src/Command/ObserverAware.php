<?php

namespace Yannoff\Handy\Command;

trait ObserverAware
{
    protected $observers = [];

    protected function attach($observer)
    {
        $this->observers[] = $observer;
    }

    protected function detach($observer)
    {
        $this->observers = array_filter(
            $this->observers,
            function($element) use ($observer) {
                return $observer != $element;
            }
        );
    }

    public function notify()
    {
        foreach ($this->observers as $observer) {
            $observer->update($this);
        }
    }
}