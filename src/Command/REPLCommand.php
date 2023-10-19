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

use Psr\Container\ContainerInterface;
use Symfony\Component\HttpKernel\Kernel as SymfonyKernel;
use Yannoff\Component\Console\Command;
use Yannoff\Component\Console\Definition\Option;
use Yannoff\Component\Console\IO\Output\Formatter;
use Yannoff\Handy\Config;

/**
 * The main REPL command class
 */
class REPLCommand extends Command
{
    use KernelAware;

    /**
     * End-of-code sequence
     * When this is detected, the code is ready to be sent
     *
     * @var string
     */
    const END = ';;';

    /**
     * Default prompt message
     *
     * @var string
     */
    const PS1 = 'PHP> ';

    /**
     * Default project dir
     *
     * @var string
     */
    protected $dir = null;

    /**
     * REPLCommand constructor.
     *
     * @param string $dir The default project dir
     */
    public function __construct(string $dir = '')
    {
        $this->dir = $dir;
        parent::__construct('REPL');
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setHelp('A basic REPL for symfony applications')
            ->addOption('working-dir', 'd', Option::VALUE, 'Project top-level directory', $this->dir)
            ->addOption('kernel', 'k', Option::VALUE, 'Alternative Kernel class FQCN', 'App\\Kernel')
            ->addOption('verbose', 'v', Option::FLAG, 'Turn on the verbose mode')
            ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute()
    {
        $kernelClass = $this->getOption('kernel');
        $appDir = $this->getOption('working-dir');

        Config::set('verbose', $this->getOption('verbose'));

        $this->initializeKernel($kernelClass, $appDir);

        $this->welcome($kernelClass);

        $line = null;
        while (!$this->isExit($line)):
            try {
                $line = null;
                $lines = [];
                while ($this->isContinuing($line)) {
                    $line = $this->prompt();
                    $lines[] = $line;
                }
                $block = implode("\n", $lines);
                eval($block);
                $this->writeln();
            } catch (\Exception $e) {
                $this->writeln((string) $e);
            } catch (\Error $e) {
                $this->writeln((string) $e);
            }
        endwhile;
    }

    /**
     * Detect whether the ending sequence is present in the given input line
     *
     * @param string|null $input
     *
     * @return bool
     */
    protected function isContinuing(string $input = null): bool
    {
        return self::END !== substr($input, -(1 * mb_strlen(self::END)));
    }

    /**
     * Check whether the given input line is an exit control sequence
     *
     * @param string|null $input
     *
     * @return bool
     */
    protected function isExit(string $input = null): bool
    {
        return in_array(trim($input), ['exit', 'quit']);
    }

    /**
     * Print the welcoming message to standard output
     *
     * @param string $kernelClass The running kernel class (defaults to SymfonyKernel)
     */
    protected function welcome(string $kernelClass = SymfonyKernel::class)
    {
        $name = $this->getApplication()->getName();
        $version = $this->getApplication()->getVersion();
        $phpVersion = sprintf('%s.%s.%s', PHP_MAJOR_VERSION, PHP_MINOR_VERSION, PHP_RELEASE_VERSION);
        $symfonyVersion = self::$kernel::VERSION;
        $endingWord = self::END;
        $this->writeln(<<<EOW

********************************************************************************************
                    Welcome to <strong>$name</strong> !

$name $version running on PHP $phpVersion / Symfony $symfonyVersion

For convenience, the following helpers have been made available:

* <strong>self::kernel()</strong> : returns the <yellow>$kernelClass</yellow> instance
* <strong>self::container()</strong> : returns the service container instance
* <strong>self::get(</strong><cyan>'service.name'</cyan><strong>)</strong> : returns the queried service instance

Hints: 

* A line ending by a <green>$endingWord</green> will trigger code eval
* Press <yellow>^C</yellow> or type <yellow>quit</yellow> to exit at any time

********************************************************************************************
EOW
        );
    }

    /**
     * @param string $prompt The readline prompt message
     *
     * @return false|string
     */
    public function prompt(string $prompt = self::PS1): string
    {
        $line = readline($prompt);
        readline_add_history($line);
        return $line;
    }

    /**
     * @return SymfonyKernel
     */
    public static function kernel(): SymfonyKernel
    {
        return self::$kernel;
    }

    /**
     * @return ContainerInterface
     */
    public static function container(): ContainerInterface
    {
        return self::kernel()->getContainer();
    }

    /**
     * @param string $name The service name
     *
     * @return mixed
     */
    public static function get(string $name)
    {
        return self::container()->get($name);
    }

    /**
     * {@inheritdoc}
     */
    public function getUsage($tab = Formatter::TAB, $width = 24)
    {
        return parent::getUsage($tab, $width);
    }
}
