<?php

namespace Phi\LacyDI;

interface DiCompilerInterface
{
    /**
     * creates an onject from given class name and inject all objects
     *
     * @param string $className
     *
     * @return mixed
     */
    public function compile(string $className);
}
