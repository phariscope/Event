<?php

declare(strict_types=1);

namespace Phariscope\Event\Tests;

use Phariscope\Event\EventDispatcher;
use PHPUnit\Framework\TestCase;

final class EventDispatcherStoppableTest extends TestCase
{
    protected function setUp(): void
    {
        EventDispatcher::tearDown();
    }

    /**
     * Creates a test listener that tracks calls and optionally stops event propagation.
     */
    private function makeListener(string $identifier, bool $shouldStopPropagation = false): TestListener
    {
        return new TestListener($identifier, $shouldStopPropagation);
    }

    /**
     * Creates a stoppable test event.
     */
    private function makeStoppableEvent(bool $allowStopping = true): TestStoppableEvent
    {
        return new TestStoppableEvent($allowStopping);
    }

    public function testStopsPropagationWhenEventBecomesStopped(): void
    {
        // Arrange
        $dispatcher = EventDispatcher::instance();
        $dispatcher->distributeImmediately();

        $listenerA = $this->makeListener('A', shouldStopPropagation: true);
        $listenerB = $this->makeListener('B', shouldStopPropagation: false);

        $dispatcher->subscribe($listenerA);
        $dispatcher->subscribe($listenerB);

        $event = $this->makeStoppableEvent(allowStopping: true);

        // Act
        $dispatcher->dispatch($event);

        // Assert
        $this->assertSame(['A'], $listenerA->getCalls());
        $this->assertSame([], $listenerB->getCalls());
    }

    public function testDoesNotStopWhenEventIsNotStopped(): void
    {
        // Arrange
        $dispatcher = EventDispatcher::instance();
        EventDispatcher::tearDown();
        $dispatcher = EventDispatcher::instance();
        $dispatcher->distributeImmediately();

        $listenerA = $this->makeListener('A', shouldStopPropagation: false);
        $listenerB = $this->makeListener('B', shouldStopPropagation: false);

        $dispatcher->subscribe($listenerA);
        $dispatcher->subscribe($listenerB);

        $event = $this->makeStoppableEvent(allowStopping: false);

        // Act
        $dispatcher->dispatch($event);

        // Assert - Both listeners should be called since propagation is not stopped
        $this->assertSame(['A'], $listenerA->getCalls());
        $this->assertSame(['B'], $listenerB->getCalls());
    }

    public function testMultipleListenersWithMixedBehavior(): void
    {
        // Arrange
        $dispatcher = EventDispatcher::instance();
        $dispatcher->distributeImmediately();

        $listener1 = $this->makeListener('1', shouldStopPropagation: false);
        $listener2 = $this->makeListener('2', shouldStopPropagation: false);
        $listener3 = $this->makeListener('3', shouldStopPropagation: true); // This one stops
        $listener4 = $this->makeListener('4', shouldStopPropagation: false); // Should not be called

        $dispatcher->subscribe($listener1);
        $dispatcher->subscribe($listener2);
        $dispatcher->subscribe($listener3);
        $dispatcher->subscribe($listener4);

        $event = $this->makeStoppableEvent(allowStopping: true);

        // Act
        $dispatcher->dispatch($event);

        // Assert - Only listeners 1, 2, and 3 should be called (3 stops propagation)
        $this->assertSame(['1'], $listener1->getCalls());
        $this->assertSame(['2'], $listener2->getCalls());
        $this->assertSame(['3'], $listener3->getCalls());
        $this->assertSame([], $listener4->getCalls());
    }
}
