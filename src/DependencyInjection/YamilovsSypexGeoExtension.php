<?php

namespace Yamilovs\SypexGeoBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Yamilovs\SypexGeoBundle\Manager\SypexGeoManager;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class YamilovsSypexGeoExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $container->setParameter($this->getAlias() . '.database_path', $config['database_path']);
        $container->setParameter($this->getAlias() . '.mode', self::convertModeToInt($config['mode']));
        $container->setParameter($this->getAlias() . '.connection', $config['connection']);
    }

    private static function convertModeToInt($mode)
    {
        $availableModes = SypexGeoManager::getAvailableModes();

        if (is_int($mode)) {
            if (in_array($mode, $availableModes)) {
                return $mode;
            }
        } else {
            if (array_key_exists($mode, $availableModes)) {
                return $availableModes[$mode];
            }
        }

        return SypexGeoManager::SXGEO_FILE;
    }
}
