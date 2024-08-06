<?php

declare(strict_types=1);

namespace Pim\Bundle\CustomEntityBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

use function array_merge;
use function array_pop;

class PimCustomEntityExtension extends Extension implements PrependExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        $loader->load('actions.yml');
        $loader->load('connectors.yml');
        $loader->load('controllers.yml');
        $loader->load('event_listeners.yml');
        $loader->load('jobs.yml');
        $loader->load('job_parameters.yml');
        $loader->load('managers.yml');
        $loader->load('mass_actions.yml');
        $loader->load('metadata.yml');
        $loader->load('savers.yml');
        $loader->load('serializer.yml');
        $loader->load('services.yml');
        $loader->load('update_guessers.yml');
    }

    /**
     * {@inheritdoc}
     */
    public function prepend(ContainerBuilder $container): void
    {
        if (!$container->hasExtension('doctrine_migrations')) {
            return;
        }

        $doctrineConfig = $container->getExtensionConfig('doctrine_migrations');
        $container->prependExtensionConfig('doctrine_migrations', [
            'migrations_paths' => array_merge(array_pop($doctrineConfig)['migrations_paths'] ?? [], [
                'Pim\Bundle\CustomEntityBundle\Migrations' => '@PimCustomEntityBundle/Migrations',
            ]),
        ]);
    }
}
