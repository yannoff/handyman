<?php

namespace Yannoff\Handy\Command;

use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\Kernel as SymfonyKernel;
use Yannoff\Handy\Kernel;
use Yannoff\Handy\Logger;

/**
 * Provide kernel initialization logic
 */
trait KernelAware
{
    /**
     * @var SymfonyKernel
     */
    protected static $kernel;

    /**
     * Initialize the handy customized kernel instance
     *
     * @param string $kernelClass
     * @param string $projectDir
     */
    protected function initializeKernel(string $kernelClass, string $projectDir)
    {
        $this->loadEnv($projectDir);
        $this->clearCache($projectDir);

        $start = microtime(true);
        self::$kernel = Kernel::create($kernelClass, $projectDir);
        $time = (microtime(true) - $start);

        Logger::debug(sprintf("Kernel loaded in: %s sec", $time));
    }

    /**
     * Load environment variables from the .env file located in $projectDir
     *
     * @param string $projectDir
     */
    protected function loadEnv(string $projectDir)
    {
        // Load cached env vars if the .env.local.php file exists
        if (is_array($env = @include $projectDir . '/.env.local.php') && (!isset($env['APP_ENV']) || ($_SERVER['APP_ENV'] ?? $_ENV['APP_ENV'] ?? $env['APP_ENV']) === $env['APP_ENV'])) {
            (new Dotenv(false))->populate($env);
            return;
        }

        // Load all the .env files otherwise
        $files = [];
        // Emulate the Dotenv::loadEnv() method behavior, to ensure BC with old versions
        foreach (['.env', '.env.local', '.env.dev', '.env.dev.local'] as $filename) {
            $filepath = $projectDir . '/' . $filename;
            if (file_exists($filepath)) {
                $files[] = $filepath;
            }
        }
        (new Dotenv(false))->load(...$files);
    }

    /**
     * Remove the $projectDir/var/cache/$env directory contents
     *
     * @param string $projectDir
     * @param string $env
     */
    protected function clearCache(string $projectDir, string $env = 'dev')
    {
        $cacheDir = sprintf('%s/var/cache/%s', $projectDir, $env);
        (new Filesystem())->remove($cacheDir);
    }
}
