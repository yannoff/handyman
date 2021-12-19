<?php

namespace Yannoff\Handy;

use Yannoff\Component\Console\Application as BaseApplication;
use Yannoff\Component\Console\Command;
use Yannoff\Component\Console\Exception\RuntimeException;

/**
 * Override base application to support real single-command behavior
 */
class Application extends BaseApplication
{
    /**
     * @param         $name
     * @param         $version
     * @param Command $main
     */
    public function __construct($name, $version, Command $main)
    {
        parent::__construct($name, $version);

        $this
            ->add($main)
            ->setDefault($main->getName())
        ;
    }

    /**
     * @param array $args
     *
     * @return int
     */
    public function run($args = [])
    {
        if (empty($args)) {
            $args = $_SERVER['argv'];
        }

        $this->script = array_shift($args);
        $command = $args[0] ?? null;
        $default = $this->getDefault();

        // Invoke the appropriated command for special global options like --help, --version, etc
        switch ($command):
            case '--version':
                return $this->get(self::COMMAND_VERS)->run([]);

            case '--help':
            case '-h':
            case '--usage':
                return $this->get(self::COMMAND_HELP)->run([$default]);
        endswitch;

        try {
            return $this->get($default)->run($args);
        } catch (RuntimeException $e) {
            $error = sprintf('%s, exiting.', (string) $e);
            $this->iowrite($error);
            return $e->getCode();
        }
    }
}
