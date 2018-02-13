<?php

namespace Phi\LacyDI\Factory;

use Phi\LacyDI\DiContainerInterface;

interface DiFactoryInterface
{

    /**
     * sets config for the factory
     *
     * @param DiContainerInterface $container
     * @param array                $config
     */
    public function setConfig(DiContainerInterface $container, array $config) : void;

    /**
     * try create a object return null if its not possible
     *
     * @param DiContainerInterface $container
     * @param string               $id
     *
     * @return null|mixed
     */
    public function factory(DiContainerInterface $container, string $id);

}
