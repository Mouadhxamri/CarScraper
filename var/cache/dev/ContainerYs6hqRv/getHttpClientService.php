<?php

namespace ContainerYs6hqRv;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class getHttpClientService extends App_KernelDevDebugContainer
{
    /**
     * Gets the private 'http_client' shared service.
     *
     * @return \Symfony\Contracts\HttpClient\HttpClientInterface
     */
    public static function do($container, $lazyLoad = true)
    {
        include_once \dirname(__DIR__, 4).''.\DIRECTORY_SEPARATOR.'vendor'.\DIRECTORY_SEPARATOR.'symfony'.\DIRECTORY_SEPARATOR.'http-client-contracts'.\DIRECTORY_SEPARATOR.'HttpClientInterface.php';
        include_once \dirname(__DIR__, 4).''.\DIRECTORY_SEPARATOR.'vendor'.\DIRECTORY_SEPARATOR.'symfony'.\DIRECTORY_SEPARATOR.'http-client'.\DIRECTORY_SEPARATOR.'HttpClient.php';

        $container->privates['http_client'] = $instance = \Symfony\Component\HttpClient\HttpClient::create([], 6);

        $instance->setLogger(($container->privates['logger'] ?? $container->getLoggerService()));

        return $instance;
    }
}
