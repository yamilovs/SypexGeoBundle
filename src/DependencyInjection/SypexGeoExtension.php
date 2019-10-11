<?php

namespace Yamilovs\Bundle\SypexGeoBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Yamilovs\SypexGeo\Database\Mode;

class SypexGeoExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter(sprintf('%s.database_path', $this->getAlias()), $config['database_path']);
        $container->setParameter(sprintf('%s.mode', $this->getAlias()), self::convertModeToInt($config['mode']));
        $container->setParameter(sprintf('%s.connection', $this->getAlias()), $config['connection']);
    }

    private static function convertModeToInt(string $mode): int
    {
        $modes = Mode::getModes();

        return array_key_exists($mode, $modes) ? $modes[$mode] : Mode::FILE;
    }

    public function getAlias(): string
    {
        return 'yamilovs_sypex_geo';
    }
}
