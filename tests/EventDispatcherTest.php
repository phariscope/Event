<?php

namespace Phariscope\Event\Tests;

use Phariscope\Event\EventDispatcher;
use Phariscope\Event\Tools\SpySubscriber;
use PHPUnit\Framework\TestCase;

class EventDispatcherTest extends TestCase
{
    public function testDispatch(): void
    {
        $spy = new SpySubscriber();
        $dispatcher = new EventDispatcher();
        $this->assertTrue(true);
    }
}
