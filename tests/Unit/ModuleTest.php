<?php

namespace Go\Laminas\Framework\Tests\Unit;

use Go\Aop\Aspect;
use Go\Core\AspectContainer;
use Go\Laminas\Framework\Module;
use Laminas\EventManager\EventManagerInterface;
use Laminas\ModuleManager\ModuleEvent;
use Laminas\ModuleManager\ModuleManagerInterface;
use Laminas\ServiceManager\ServiceManager;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;

/**
 * @package Go\Laminas\Framework\Tests\Unit
 */
class ModuleTest extends TestCase
{
    /**
     * @test
     */
    public function itReturnsConfig()
    {
        $module = new Module();

        $this->assertInternalType(
            'array',
            $module->getConfig(),
            'returned config should be of type array'
        );
    }

    /**
     * @test
     */
    public function itAttachesListenerOnInit()
    {
        $module = new Module();

        $eventManager = $this->prophesize(EventManagerInterface::class);
        $eventManager->attach(
            Argument::exact(ModuleEvent::EVENT_LOAD_MODULES_POST),
            Argument::exact([$module, 'initializeAspects'])
        )->shouldBeCalled();

        $moduleManager = $this->prophesize(ModuleManagerInterface::class);
        $moduleManager->getEventManager()->willReturn($eventManager->reveal());

        $module->init($moduleManager->reveal());
    }

    /**
     * @test
     */
    public function itRegisterAspectsOnInitializeAspects()
    {
        $aspect = $this->prophesize(Aspect::class)->reveal();

        $aspectContainer = $this->prophesize(AspectContainer::class);
        $aspectContainer->registerAspect(Argument::exact($aspect))
            ->shouldBeCalled();

        $serviceManager = $this->prophesize(ServiceManager::class);
        $serviceManager->get(AspectContainer::class)
            ->willReturn($aspectContainer->reveal())
            ->shouldBeCalled();
        $serviceManager->get('config')
            ->willReturn([Module::ASPECT_CONFIG_KEY => ['testAspect']])
            ->shouldBeCalled();
        $serviceManager->get('testAspect')
            ->willReturn($aspect)
            ->shouldBeCalled();

        $moduleEvent = $this->prophesize(ModuleEvent::class);
        $moduleEvent->getParam('ServiceManager')
            ->willReturn($serviceManager->reveal());

        $module = new Module();

        $module->initializeAspects($moduleEvent->reveal());
    }
}