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

use Symfony\Component\HttpKernel\Kernel as SymfonyKernel;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Yannoff\Handy\DependencyInjection\Compiler\ExposeServicesPass;
use Yannoff\Handy\Exception\InvalidKernelClassException;
use Yannoff\Handy\Event\Dispatcher;
use Yannoff\Handy\Event\KernelEvents;

/**
 * Customized kernel factory class
 * Create a new instance on top of the application's kernel,
 * with the apposite method overrides
 */
class Kernel
{
    /**
     * Kernel factory method
     *
     * @param string $kernelClass The application's kernel fully-qualified classname
     * @param string $projectDir  The return value for the getProjectDir() method
     * @param string $env         The base kernel $env constructor argument
     * @param bool   $debug       The base kernel $debug constructor argument
     *
     * @return SymfonyKernel
     */
    public static function create(string $kernelClass, string $projectDir, string $env = 'dev', bool $debug = true): SymfonyKernel
    {
        if (!class_exists($kernelClass)) {
            throw new InvalidKernelClassException($kernelClass);
        }

        // @source https://stackoverflow.com/a/37895055
        class_alias($kernelClass, __NAMESPACE__ . '\BaseKernel');

        $kernel = new class ($env, $debug, $projectDir) extends BaseKernel {
            /**
             * Custom project dir
             *
             * @var string
             */
            private $projectDir;

            /**
             * @param string $env
             * @param string $debug
             * @param string $dir
             */
            public function __construct($env, $debug, $dir)
            {
                $this->projectDir = $dir;
                parent::__construct($env, $debug);
            }

            /**
             * Override the base method which gives a wrong project dir
             *
             * @return string
             */
            public function getProjectDir(): string
            {
                return $this->projectDir;
            }

            /**
             * Override base method to add the handyman specific compiler pass
             *
             * @param ContainerBuilder $container
             */
            protected function build(ContainerBuilder $container)
            {
                Logger::debug(">>> Building container");
                parent::build($container);
                $container->addCompilerPass(new ExposeServicesPass(), PassConfig::TYPE_BEFORE_OPTIMIZATION, 99999);
            }
        };

        $kernel->boot();

        Dispatcher::trigger(KernelEvents::BOOTED, $kernel->getContainer());

        return $kernel;
    }
}
