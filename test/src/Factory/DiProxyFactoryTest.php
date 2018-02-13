<?php

namespace Phi\LacyDITest\Factory;

use Phi\LacyDI\DiCompilerInterface;
use Phi\LacyDI\DiContainerInterface;
use Phi\LacyDI\Factory\DiProxyFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ProxyManager\Factory\LazyLoadingValueHolderFactory;

class DiProxyFactoryTest extends TestCase
{
    /** @var DiProxyFactory */
    private $factory;

    protected function setUp()
    {
        parent::setUp();
        $this->factory = new DiProxyFactory();
    }

    /**
     * @throws \Exception
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function testFactory()
    {
        list($container) = $this->getContainerMock();
        $this->factory->setConfig($container, []);
        $object = $this->factory->factory($container,TestClass::class);
        self::assertInstanceOf(TestClass::class, $object);
    }

    /**
     * @throws \Exception
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function testFactoryFromInterface()
    {
        list($container) = $this->getContainerMock();
        $this->factory->setConfig($container, [
            TestClassInterface::class => TestClass::class
        ]);
        $object = $this->factory->factory($container,TestClassInterface::class);
        self::assertInstanceOf(TestClass::class, $object);
    }

    /**
     * @throws \Exception
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function testFactoryProxy()
    {
        list($container) = $this->getContainerMock();
        $this->factory->setConfig($container, []);
        $object = $this->factory->factory($container,TestClass::class);
        self::assertNotEquals(TestClass::class, get_class($object));
    }

    /**
     * @throws \Exception
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function testFactoryProxyInitializer()
    {
        /** @var MockObject $diCompiler */
        list($container, $diCompiler) = $this->getContainerMock();
        $this->factory->setConfig($container, [
            TestClassInterface::class => TestClass::class
        ]);
        $diCompiler->expects(self::once())
            ->method('compile')
            ->with(TestClass::class)
            ->willReturn(new TestClass());
        $object = $this->factory->factory($container,TestClass::class);
        self::assertNotEquals(TestClass::class, get_class($object));
        self::assertTrue($object->doSomethingExiting());
    }

    /**
     * build the di container
     * @return array
     *
     * @throws \ReflectionException
     */
    private function getContainerMock()
    {
        $diCompilerMock = self::createMock(DiCompilerInterface::class);
        $container = self::createMock(DiContainerInterface::class);
        $container->expects(self::at(0))
            ->method('get')
            ->with(LazyLoadingValueHolderFactory::class)
            ->willReturn(new LazyLoadingValueHolderFactory());
        $container->expects(self::at(1))
            ->method('get')
            ->with(DiCompilerInterface::class)
            ->willReturn($diCompilerMock);
        return [$container, $diCompilerMock];
    }

}


interface TestClassInterface
{
}

class TestClass implements TestClassInterface
{
    public function doSomethingExiting()
    {
        return true;
    }
}
