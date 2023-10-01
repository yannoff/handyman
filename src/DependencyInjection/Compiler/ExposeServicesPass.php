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

namespace Yannoff\Handy\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Yannoff\Handy\Logger;

/**
 * Iterate over service definitions to make them all public
 */
class ExposeServicesPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $definitions = $container->getDefinitions();
        ksort($definitions);

        foreach ($definitions as $name => $definition) {
            if ($this->mustBeSkipped($name)) {
                $this->log($name, 'skip');
                continue;
            }
            $this->log($name);
            $definition->setPublic(true);
        }
    }

    /**
     * Log the action performed on the given service
     *
     * @param string $name The service name
     * @param string $type The type of action "skip" or "process" (defaults to "process")
     */
    protected function log(string $name, string $type = 'process')
    {
        switch ($type):
            case 'process':
                $template = "<grey>Setting </grey>%s<grey> visibility to public...</grey>";
                break;
            case 'skip':
                $template = "<grey>Skipping </grey>%s<grey> ...</grey>";
                break;
        endswitch;

        $message = sprintf($template, $name);

        Logger::debug($message);
    }

    /**
     * Determine whether the given service must be skipped (ie not processed)
     *
     * Blacklisted services are not processed, as well as internal temporary
     * services (those named in the form ".1G_xyz123" or "2_ApcCache~r3o36qz")
     *
     * @param string $name The service name
     *
     * @return bool
     */
    protected function mustBeSkipped(string $name): bool
    {
         return $this->isBlackListed($name) || $this->isInternal($name);
    }

    /**
     * Determine whether the given service is blacklisted
     *
     * Some services have messed up definitions,
     * processing them make the compiler pass crash
     *
     * @param string $name The service name
     *
     * @return bool
     */
    protected function isBlackListed(string $name): bool
    {
        $blacklist = [
            'session.abstract_handler', // is abstract: A string or a connection object.
            'annotations.filesystem_cache', // is abstract: Cache-Directory.
            'session.storage.factory.service', // dependency on a non-existent service "session.storage"
            'templating.loader.cache', // dependency on a non-existent service "templating.loader.wrapped"
            'translator.logging', // dependency on a non-existent service "translator.logging.inner"
            'annotations.filesystem_cache_adapter', // Argument 3 of service "annotations.filesystem_cache_adapter" is abstract: Cache-Directory.
            'security.context_listener', // Argument 3 of service "security.context_listener" is abstract: Provider Key.
            'jms_serializer.metadata.doctrine_phpcr_type_driver', // dependency on a non-existent service "doctrine_phpcr"
            'jms_serializer.metadata.doc_block_driver', // dependency on a non-existent service "jms_serializer.metadata.doc_block_driver.inner"
            'jms_serializer.doctrine_phpcr_object_constructor', // dependency on a non-existent service "doctrine_phpcr"
        ];

        return in_array($name, $blacklist);
    }

    /**
     * Determine whether the given service is internal
     *
     * Making those services public does not make any sense from the handyman perspective
     *
     * @param string $name The service name
     *
     * @return bool
     */
    protected function isInternal(string $name): bool
    {
        return preg_match('/^\./', $name) || preg_match('/^[0-9]/', $name);
    }
}
