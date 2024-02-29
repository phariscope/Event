<?php

namespace Phariscope\Tests;

use Phariscope\Event\ListenerProvider;
use Phariscope\Event\Tests\EventSent;
use Phariscope\Event\Tools\SpySubscriber;
use PHPUnit\Framework\TestCase;

class ListenerProviderTest extends TestCase
{
    public function testGetListenersForEvent(): void
    {
        $spy = new SpySubscriber();

        $event = new EventSent("unId");

        $listener = new ListenerProvider();
        $listener->addListener(EventSent::class, $spy);
        $listeners =  $listener->getListenersForEvent($event);
        $this->assertIsIterable($listeners);
        $this->assertEquals($spy, $listeners[0]);
    }
}
