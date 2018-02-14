<?php

namespace Phi\LacyDI\Factory;

use Phi\LacyDI\DiCompilerInterface;
use Phi\LacyDI\DiContainerInterface;
use ProxyManager\Factory\LazyLoadingValueHolderFactory;

class DiProxyFactory implements DiFactoryInterface
{

    /** @var array */
    private $config;

    /** @var LazyLoadingValueHolderFactory */
    private $proxyFactory;

    /** @var DiCompilerInterface */
    private $diCompiler;

    /**
     * {@inheritdoc}
     *
     * @param DiContainerInterface $container
     * @param array                $config
     *
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function setConfig(DiContainerInterface $container, array $config): void
    {
        $this->proxyFactory = $container->get(LazyLoadingValueHolderFactory::class);
        $this->diCompiler = $container->get(DiCompilerInterface::class);
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     *
     * @param DiContainerInterface $container
     * @param string               $id
     *
     * @return object
     */
    public function factory(DiContainerInterface $container, string $id) : object
    {
        $className = $id;
        if (isset($this->config[$className])) {
            $className = $this->config[$className];
        }
        return $this->createProxy($className);
    }

    /**
     * @param string $className
     *
     * @return object
     */
    private function createProxy(string $className) : object
    {
        return $this->proxyFactory->createProxy(
            $className,
            function (&$wrappedObject, $proxy, $method, $parameters, &$initializer) use ($className) {
                $initializer = null;
                $wrappedObject = $this->diCompiler->compile($className);
                return true;
            }
        );
    }

}
