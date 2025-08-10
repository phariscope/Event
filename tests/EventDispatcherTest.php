<?php

declare(strict_types=1);

namespace Phariscope\Event\Tests;

use Phariscope\Event\EventDispatcher;
use Phariscope\Event\Tools\SpyListener;
use PHPUnit\Framework\TestCase;

class EventDispatcherTest extends TestCase
{
    public function testDispatch(): void
    {
        // Arrange
        $spy = new SpyListener();
        $dispatcher = EventDispatcher::instance();

        // Act
        $dispatcher->distribute();

        // Assert
        $this->assertEquals(0, $spy->handleCallCount);
    }

    public function testIsDistributeImmediately(): void
    {
        // Arrange
        $dispatcher = EventDispatcher::instance();

        // Act
        $dispatcher->distributeImmediately();

        // Assert
        $this->assertTrue($dispatcher->isImmediateDistributionEnabled());
    }

    public function testDisableImmediateDistribution(): void
    {
        // Arrange
        $dispatcher = EventDispatcher::instance();

        // Act
        $dispatcher->disableImmediateDistribution();

        // Assert
        $this->assertFalse($dispatcher->isImmediateDistributionEnabled());
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
}
