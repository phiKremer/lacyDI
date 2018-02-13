<?php

namespace Phi\LacyDITest;

use Phi\LacyDI\DiContainer;
use Phi\LacyDI\DiContainerInterface;
use Phi\LacyDI\Factory\DiFactoryInterface;
use Phi\LacyDI\Exception\ContainerException;
use Phi\LacyDI\Exception\NotFoundException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class DiContainerTest extends TestCase
{
    /** @var DiContainer */
    private $container;

    /** @var MockObject */
    private $factoryMock;

    /**
     * @throws \ReflectionException
     */
    protected function setUp()
    {
        parent::setUp();
        $this->container = new DiContainer();
        $this->factoryMock = self::createMock(DiFactoryInterface::class);
        $this->container->addFactory($this->factoryMock);
    }

    /**
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function testGetNotRegistered()
    {
        self::expectException(NotFoundException::class);
        $this->container->get('a');
    }

    /**
     * @throws \Exception
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function testRegisterAndGetService()
    {
        $service = new \stdClass();
        $this->container->setService('test', $service);
        self::assertTrue($this->container->has('test'));
        self::assertSame($service, $this->container->get('test'));
    }

    /**
     * @throws \Exception
     */
    public function testRegisterAndHasService()
    {
        self::assertFalse($this->container->has('test'));
        $this->container->setService('test', 'omg');
        self::assertTrue($this->container->has('test'));
    }

    /**
     * @throws \Exception
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function testFactoryService()
    {
        $this->factoryMock->expects(self::once())
            ->method('factory')
            ->with($this->container , 'test')
            ->willReturn('testObject');

        self::assertTrue($this->container->has('test'));
        self::assertSame('testObject', $this->container->get('test'));
    }

    public function testFactoryException()
    {
        self::expectException(ContainerException::class);
        $this->factoryMock->expects(self::once())
            ->method('factory')
            ->with($this->container, 'test')
            ->willThrowException(new \Exception());
        $this->container->get('test');
    }

    /**
     * @throws \Exception
     */
    public function testCreateFromConfig()
    {

        $config = [
            TestFactory::class => [
                'config'
            ]
        ];

        $container = DiContainer::createFromConfig($config);
        $reflectContainer = new \ReflectionObject($container);
        $propFactories = $reflectContainer->getProperty('factories');
        $propFactories->setAccessible(true);
        $factories = $propFactories->getValue($container);

        self::assertSame(1, count($factories));
        /** @var TestFactory $factory */
        $factory = array_pop($factories);
        self::assertInstanceOf(TestFactory::class, $factory);
        self::assertSame(['config'], $factory->config);
    }
}

class TestFactory implements DiFactoryInterface
{
    public $config;

    public function setConfig(DiContainerInterface $container, array $config): void
    {
        $this->config = $config;
    }

    public function factory(DiContainerInterface $container, string $id)
    {
    }
}
