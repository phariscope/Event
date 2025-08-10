<?php

declare(strict_types=1);

namespace Phariscope\Event\Tests;

use PHPUnit\Framework\TestCase;

/**
 * Tests for the extracted test helper classes.
 *
 * This test demonstrates that TestListener and TestStoppableEvent
 * can be used independently and have their own functionality.
 */
final class TestClassesTest extends TestCase
{
    public function testTestListenerFunctionality(): void
    {
        // Arrange
        $listener = new TestListener('test-listener', shouldStopPropagation: false);

        // Assert initial state
        $this->assertSame('test-listener', $listener->getIdentifier());
        $this->assertFalse($listener->shouldStopPropagation());
        $this->assertSame([], $listener->getCalls());
        $this->assertSame(0, $listener->getCallCount());
        $this->assertFalse($listener->wasCalled());

        // Simulate event handling
        $event = new TestStoppableEvent();
        $this->assertTrue($listener->isSubscribedTo($event));
        $this->assertTrue($listener->handle($event));

        // Assert after handling
        $this->assertSame(['test-listener'], $listener->getCalls());
        $this->assertSame(1, $listener->getCallCount());
        $this->assertTrue($listener->wasCalled());

        // Test reset functionality
        $listener->resetCalls();
        $this->assertSame([], $listener->getCalls());
        $this->assertFalse($listener->wasCalled());
    }

    public function testTestStoppableEventFunctionality(): void
    {
        // Test stoppable event
        $stoppableEvent = TestStoppableEvent::createStoppable();
        $this->assertTrue($stoppableEvent->allowsStopping());
        $this->assertFalse($stoppableEvent->isPropagationStopped());

        $stoppableEvent->stop();
        $this->assertTrue($stoppableEvent->isPropagationStopped());

        $stoppableEvent->reset();
        $this->assertFalse($stoppableEvent->isPropagationStopped());

        // Test non-stoppable event
        $nonStoppableEvent = TestStoppableEvent::createNonStoppable();
        $this->assertFalse($nonStoppableEvent->allowsStopping());
        $this->assertFalse($nonStoppableEvent->isPropagationStopped());

        $nonStoppableEvent->stop(); // Should not work
        $this->assertFalse($nonStoppableEvent->isPropagationStopped());

        $nonStoppableEvent->forceStop(); // Should work
        $this->assertTrue($nonStoppableEvent->isPropagationStopped());
    }

    public function testTestListenerWithStopPropagation(): void
    {
        // Arrange
        $listener = new TestListener('stopper', shouldStopPropagation: true);
        $event = TestStoppableEvent::createStoppable();

        // Act
        $listener->handle($event);

        // Assert
        $this->assertTrue($listener->shouldStopPropagation());
        $this->assertTrue($event->isPropagationStopped());
        $this->assertSame(['stopper'], $listener->getCalls());
    }

    public function testCustomOccurredOnDate(): void
    {
        // Arrange
        $customDate = new \DateTimeImmutable('2023-01-01 12:00:00');

        // Act
        $event = new TestStoppableEvent(allowStopping: true, occurredOn: $customDate);

        // Assert
        $this->assertEquals($customDate, $event->occurredOn());
    }
}
