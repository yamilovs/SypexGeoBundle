<?php

namespace YamilovS\SypexGeoBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use YamilovS\SypexGeoBundle\Manager\SypexGeoManager;

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

        $container->setParameter($this->getAlias().'.database_path', $config['database_path']);
        $container->setParameter($this->getAlias().'.mode', $this->modeToInt($config['mode']));
        $container->setParameter($this->getAlias().'.connection', $config['connection']);
    }

    /**
     * @param string|int $mode
     * @return int
     */
    private function modeToInt($mode)
    {
        if (is_int($mode)){
            return (int)$mode;
        }
        $reflectionClass = new \ReflectionClass(SypexGeoManager::class);
        $constants = $reflectionClass->getConstants();
        if (isset($constants[$mode])){
            return (int)$constants[$mode];
        }
        return 0;
    }
}
