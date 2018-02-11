<?php

namespace Phi\LacyDITest;

use Phi\LacyDI\DiContainer;
use Phi\LacyDI\Exception\NotFoundException;
use PHPUnit\Framework\TestCase;

class DiContainerTest extends TestCase
{
    /** @var DiContainer */
    private $container;

    protected function setUp()
    {
        parent::setUp();
        $this->container = new DiContainer();
    }

    public function testGetNotRegistered()
    {
        self::expectException(NotFoundException::class);
        $this->container->get('a');
    }

    public function testSetService()
    {
        self::assertTrue($this->container->setService('test', 'a'));
    }

    public function testRegisterAndGetService()
    {
        $service = new \stdClass();
        self::assertTrue($this->container->setService('test', $service));
        self::assertTrue($this->container->has('test'));
        self::assertSame($service, $this->container->get('test'));
    }

    public function testHas()
    {
        self::assertFalse($this->container->has('test'));
        self::assertTrue($this->container->setService('test', 'omg'));
        self::assertTrue($this->container->has('test'));
    }

}
