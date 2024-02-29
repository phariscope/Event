<?php

namespace Phariscope\Event\Tests;

use Phariscope\Event\EventDispatcher;
use Phariscope\Event\Tools\SpyListener;
use PHPUnit\Framework\TestCase;

class EventDispatcherTest extends TestCase
{
    public function testDispatch(): void
    {
        $spy = new SpyListener();
        $dispatcher = EventDispatcher::instance();
        $dispatcher->distribute();
        $this->assertEquals(0, $spy->handleCallCount);
    }
}
