<?php

declare(strict_types=1);

namespace Phariscope\Event\Tests;

use Phariscope\Event\EventDispatcher;
use Phariscope\Event\Tools\SpyListener;
use PHPUnit\Framework\TestCase;

class EventDispatcherTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        EventDispatcher::tearDown();
    }

    protected function tearDown(): void
    {
        EventDispatcher::tearDown();
        parent::tearDown();
    }

    public function testDispatch0Event(): void
    {
        // Arrange
        $spy = new SpyListener();
        $sut = EventDispatcher::instance();
        $sut->subscribe($spy);

        // Act
        $sut->distribute();

        // Assert
        $this->assertEquals(0, $spy->handleCallCount);
    }

    public function testDispatch1Event(): void
    {
        // Arrange
        $spy = new SpyListener();
        $sut = EventDispatcher::instance();
        $sut->subscribe($spy);

        // Act
        $sut->dispatch(new EventSent("unId"));
        $sut->distribute();

        // Assert
        $this->assertEquals(1, $spy->handleCallCount);
        $this->assertInstanceOf(EventSent::class, $spy->domainEvent);
        $this->assertEquals("unId", $spy->domainEvent->id());
    }

    public function testIsDistributeImmediately(): void
    {
        // Arrange
        $spy = new SpyListener();
        $sut = EventDispatcher::instance();
        $sut->subscribe($spy);
        $sut->distributeImmediately();

        // Act
        $sut->dispatch(new EventSent("unId"));

        // Assert
        $this->assertTrue($sut->isImmediateDistributionEnabled());
        $this->assertEquals(1, $spy->handleCallCount);
    }

    public function testDisableImmediateDistribution(): void
    {
        // Arrange
        $spy = new SpyListener();
        $sut = EventDispatcher::instance();
        $sut->subscribe($spy);

        // Act
        $sut->disableImmediateDistribution();
        $sut->dispatch(new EventSent("unId"));

        // Assert
        $this->assertFalse($sut->isImmediateDistributionEnabled());
        $this->assertEquals(0, $spy->handleCallCount);
    }

    public function testClearSubscribers(): void
    {
        // Arrange
        $dispatcher = EventDispatcher::instance();
        $dispatcher->subscribe(new SpyListener());

        // Act
        $dispatcher->clearSubscribers();

        // Assert
        $this->assertEmpty($dispatcher->getSubscribers());
    }

    public function testHasSubscriber(): void
    {
        // Arrange
        $sut = EventDispatcher::instance();
        $listener = new SpyListener();

        // Act
        $sut->subscribe($listener);

        // Assert
        $this->assertTrue($sut->hasSubscriber($listener));
        $this->assertTrue($sut->hasSubscriber(SpyListener::class));
        $this->assertFalse($sut->hasSubscriber('ListenerInexistant'));
    }
}
