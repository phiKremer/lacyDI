<?php

namespace Phi\LacyDI;

use Exception;
use Phi\LacyDI\Exception\ContainerException;
use Phi\LacyDI\Exception\NotFoundException;
use Phi\LacyDI\Factory\DiFactoryInterface;

class DiContainer implements DiContainerInterface
{
    /** @var array */
    private $registry = [];

    /** @var array */
    private $factories = [];

    /**
     * add a factory that create objects
     *
     * @param DiFactoryInterface $factory
     */
    public function addFactory(DiFactoryInterface $factory): void
    {
        $this->factories[] = $factory;
    }

    /**
     * {@inheritdoc}
     *
     * @param string $id
     *
     * @throws ContainerException
     * @throws NotFoundException
     *
     * @return mixed
     */
    public function get($id)
    {
        if (!isset($this->registry[$id])) {
            $this->tryCreateService($id);
        }
        return $this->registry[$id];
    }

    /**
     * @param string $id
     *
     * @throws ContainerException
     * @throws NotFoundException
     */
    private function tryCreateService(string $id): void
    {
        try {
            $this->createServiceViaFactory($id);
        } catch (Exception $ex) {
            throw new ContainerException();
        }
        if (!isset($this->registry[$id])) {
            throw new NotFoundException();
        }
    }

    /**
     * @param string $id
     *
     * @throws Exception
     */
    private function createServiceViaFactory(string $id): void
    {
        foreach ($this->factories as $factory) {
            $service = $factory->factory($this, $id);
            if (null !== $service) {
                $this->setService($id, $service);
                return;
            }
        }
    }

    /**
     * {@inheritdoc}
     *
     * @param string $id
     *
     * @return bool
     */
    public function has($id): bool
    {
        try {
            $this->get($id);
        } catch (NotFoundException | ContainerException $ex) {
            return false;
        }
        return true;
    }

    /**
     * Register a service to registry
     *
     * @param string $name
     * @param mixed  $service
     *
     * @return void
     */
    public function setService(string $name, $service): void
    {
        $this->registry[$name] = $service;
    }

    /**
     * creates a DiContainer with configured factories
     *
     * @param array $config
     *
     * @return DiContainer
     */
    public static function createFromConfig(array $config) : DiContainer
    {
        $container = new DiContainer();
        foreach ($config as $factoryClass => $factoryConfig) {
            /** @var DiFactoryInterface $factory */
            $factory = new $factoryClass();
            $container->addFactory($factory);
            $factory->setConfig($container, $factoryConfig);
        }
        return $container;
    }

}
