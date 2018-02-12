<?php

namespace Phi\LacyDI;

interface DiFactoryInterface
{

    /**
     * sets config for the factory
     *
     * @param array $config
     */
    public function setConfig(array $config) : void;

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
