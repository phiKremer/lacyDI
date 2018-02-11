<?php

namespace Phi\LacyDI;

use Interop\Container\ContainerInterface;
use Phi\LacyDI\Exception\NotFoundException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class DiContainer implements ContainerInterface
{
    private $registry = [];

    /**
     * {@inheritdoc}
     *
     * @param string $id
     *
     * @throws NotFoundExceptionInterface  No entry was found for **this** identifier.
     * @throws ContainerExceptionInterface Error while retrieving the entry.
     *
     * @return mixed
     */
    public function get($id)
    {
        if (!isset($this->registry[$id])) {
            throw new NotFoundException();
        }
        return $this->registry[$id];
    }


    /**
     * {@inheritdoc}
     *
     * @param string $id
     *
     * @return bool
     */
    public function has($id)
    {
        return isset($this->registry[$id]);
    }

    /**
     * Register a service to registry
     * @param string $name
     * @param mixed $service
     *
     * @return bool
     */
    public function setService(string $name, $service) : bool
    {
        $this->registry[$name] = $service;
        return true;
    }

}
