<?php

declare(strict_types=1);

namespace Phariscope\Event\Tests;

use Phariscope\Event\EventDispatcher;
use Phariscope\Event\Tools\SpyListener;
use PHPUnit\Framework\TestCase;

final class EventDispatcherUtilityMethodsTest extends TestCase
{
    protected function setUp(): void
    {
        EventDispatcher::tearDown();
    }

    public function testGetQueuedEventCount(): void
    {
        // Arrange
        $dispatcher = EventDispatcher::instance();

        // Assert
        $this->assertEquals(0, $dispatcher->getQueuedEventCount());
        $this->assertFalse($dispatcher->hasQueuedEvents());

        // Act
        $dispatcher->dispatch(new EventSent('test1'));
        $this->assertEquals(1, $dispatcher->getQueuedEventCount());
        $this->assertTrue($dispatcher->hasQueuedEvents());

        // Act
        $dispatcher->dispatch(new EventSent('test2'));

        // Assert
        $this->assertEquals(2, $dispatcher->getQueuedEventCount());

        // Act
        $dispatcher->distribute();

        // Assert
        $this->assertEquals(0, $dispatcher->getQueuedEventCount());
        $this->assertFalse($dispatcher->hasQueuedEvents());
    }

    public function testGetSubscribers(): void
    {
        // Arrange
        $dispatcher = EventDispatcher::instance();

        // Assert
        $this->assertEquals([], $dispatcher->getSubscribers());
        $this->assertEquals(0, $dispatcher->getSubscriberCount());

        $listener1 = new SpyListener();
        $listener2 = new SpyListener();

        // Act
        $dispatcher->subscribe($listener1);

        // Assert
        $this->assertEquals(1, $dispatcher->getSubscriberCount());
        $this->assertContains($listener1, $dispatcher->getSubscribers());

        // Act
        $dispatcher->subscribe($listener2);

        // Assert
        $this->assertEquals(2, $dispatcher->getSubscriberCount());
        $this->assertContains($listener1, $dispatcher->getSubscribers());
        $this->assertContains($listener2, $dispatcher->getSubscribers());
    }
}
