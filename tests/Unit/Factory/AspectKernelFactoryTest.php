<?php

namespace Go\Laminas\Framework\Tests\Unit\Factory;

use Go\Core\AspectKernel;
use Go\Laminas\Framework\Factory\AspectKernelFactory;
use Go\Laminas\Framework\Module;
use Laminas\ServiceManager\ServiceLocatorInterface;
use PHPUnit\Framework\TestCase;

/**
 * @package Go\Laminas\Framework\Tests\Unit\Factory
 */
class AspectKernelFactoryTest extends TestCase
{
    /**
     * @test
     */
    public function itCreatesKernelOnInvoke()
    {
        $serviceLocator = $this->prophesize(ServiceLocatorInterface::class);
        $serviceLocator->get('config')
            ->willReturn([Module::CONFIG_KEY => require __DIR__ . '/../../resources/goaop_module.php'])
            ->shouldBeCalled();

        $factory = new AspectKernelFactory();

        $instance = $factory($serviceLocator->reveal(), AspectKernel::class);

        $this->assertInstanceOf(
            AspectKernel::class,
            $instance,
            'factory should return an instance of ' . AspectKernel::class
        );
    }

    /**
     * @test
     */
    public function itCreatesKernelOnCreateService()
    {
        $serviceLocator = $this->prophesize(ServiceLocatorInterface::class);
        $serviceLocator->get('config')
            ->willReturn([Module::CONFIG_KEY => require __DIR__ . '/../../resources/goaop_module.php'])
            ->shouldBeCalled();

        $factory = new AspectKernelFactory();

        $instance = $factory->createService($serviceLocator->reveal());

        $this->assertInstanceOf(
            AspectKernel::class,
            $instance,
            'factory should return an instance of ' . AspectKernel::class
        );
    }
}