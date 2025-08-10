<?php

declare(strict_types=1);

namespace Phariscope\Event\Tests;

use PHPUnit\Framework\TestCase;

class EventSentTest extends TestCase
{
    public function testEventSent(): void
    {
        // Arrange
        $now = new \DateTimeImmutable();

        // Act
        $event = new EventSent("id1", $now);

        // Assert
        $this->assertEquals("id1", $event->id());
        $this->assertEquals($now, $event->occurredOn());
    }
}
