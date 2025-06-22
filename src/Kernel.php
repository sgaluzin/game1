<?php
namespace App;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

class Kernel extends BaseKernel
{
    protected function configureContainer(ContainerConfigurator $container): void
    {
        $container->import('../config/{packages}/*.yaml');
        $container->import('../config/{services}.yaml');
    }

    protected function configureRoutes(RoutingConfigurator $routes): void
    {
        $routes->import('../config/{routes}.yaml');
    }

    public function registerBundles(): iterable
    {
        $contents = require dirname(__DIR__).'/config/bundles.php';
        foreach ($contents as $class => $envs) {
            if ($envs[$this->environment] ?? $envs['all'] ?? false) {
                yield new $class();
            }
        }
    }

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load(dirname(__DIR__).'/config/packages/*.yaml', 'glob');
        $loader->load(dirname(__DIR__).'/config/services.yaml');
    }
}
