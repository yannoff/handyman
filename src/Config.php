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

/**
 * Registry for component-wide accessible config settings
 */
class Config
{
    /**
     * @var array
     */
    protected static $settings = [];

    /**
     * @param string $name
     * @param mixed  $value
     *
     * @return void
     */
    public static function set(string $name, $value)
    {
        self::$settings[$name] = $value;
    }

    /**
     * Get the queried setting, with an optional fallback value
     *
     * @param string $name
     * @param ?mixed $default
     *
     * @return mixed|null
     */
    public static function get(string $name, $default = null)
    {
        return self::$settings[$name] ?? $default;
    }
}
