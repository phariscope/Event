<?php

namespace Phariscope\Event\Tests;

use PHPUnit\Framework\TestCase;

class EventSentTest extends TestCase
{
    public function testEventSent(): void
    {
        $now = new \DateTimeImmutable();
        $event = new EventSent("id1", $now);

        $this->assertEquals("id1", $event->id());
        $this->assertEquals($now, $event->occurredOn());
    }
}
