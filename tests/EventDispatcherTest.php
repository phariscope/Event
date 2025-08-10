<?php

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
}
