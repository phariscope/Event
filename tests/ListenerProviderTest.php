<?php

namespace Phariscope\Event\Tests;

use Phariscope\Event\EventDispatcher;
use Phariscope\Event\ListenerProvider;
use Phariscope\Event\Tests\EventSent;
use Phariscope\Event\Tests\SomeListeners\CallAnotherEventListener;
use Phariscope\Event\Tests\SomeListeners\LoopListener;
use Phariscope\Event\Tools\SpyListener;
use PHPUnit\Framework\TestCase;

class ListenerProviderTest extends TestCase
{
    public function testGetListenersForEvent(): void
    {
        $spy = new SpyListener();

        $event = new EventSent("unId");

        $listener = new ListenerProvider();
        $listener->addListener(EventSent::class, $spy);
        $listeners =  $listener->getListenersForEvent($event);
        $this->assertIsIterable($listeners);
        $this->assertEquals($spy, $listeners[0]);
    }

    //public function testAListenerInfiniteLoopOfEvent():void
    //{
    //    $listener = new LoopListener();
    //
    //    $dispatcher = EventDispatcher::instance();
    //    $dispatcher->subscribe($listener);
//
    //    $event = new EventSent("unId");
    //    $dispatcher->dispatch($event);
//
    //    $dispatcher->distribute();
//
    //    $this->assertEquals(2, count($listener->traces));
    //    /** @var EventSent */
    //    $e1 = $listener->traces[0];
    //    $this->assertEquals("unId", $e1->id());
    //    /** @var EventSent */
    //    $e1 = $listener->traces[1];
    //    $this->assertEquals("2", $e1->id());
    //}

    public function testAListenerDispatchAnotherEventOfDifferentType(): void
    {
        $listener = new CallAnotherEventListener();

        $spy = new SpyListener();

        $dispatcher = EventDispatcher::instance();
        $dispatcher->subscribe($spy);
        $dispatcher->subscribe($listener);

        $event = new EventSent("unId");
        $dispatcher->dispatch($event);

        $dispatcher->distribute();

        $this->assertEquals(2, count($spy->traces));
        /** @var EventSent */
        $e1 = $spy->traces[0];
        $this->assertEquals("unId", $e1->id());
        /** @var EventSent */
        $e1 = $spy->traces[1];
        $this->assertEquals("2", $e1->id());
    }
}
