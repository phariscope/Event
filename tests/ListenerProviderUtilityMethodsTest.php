<?php

declare(strict_types=1);

namespace Phariscope\Event\Tests;

use Phariscope\Event\ListenerProvider;
use Phariscope\Event\Psr14\Event;
use Phariscope\Event\Tools\SpyListener;
use PHPUnit\Framework\TestCase;

final class ListenerProviderUtilityMethodsTest extends TestCase
{
    public function testGetListenersForEventType(): void
    {
        // Arrange
        $provider = new ListenerProvider();
        $listener = new SpyListener();

        // Assert
        $this->assertEquals([], $provider->getListenersForEventType(EventSent::class));
        $this->assertFalse($provider->hasListenersForEventType(EventSent::class));

        // Act
        $provider->addListener(EventSent::class, $listener);

        // Assert
        $this->assertEquals([$listener], $provider->getListenersForEventType(EventSent::class));
        $this->assertTrue($provider->hasListenersForEventType(EventSent::class));
    }

    public function testWithNonExistentEventType(): void
    {
        // Arrange
        $provider = new ListenerProvider();
        $nonExistentEvent = new class extends Event {
        };

        // Assert
        $this->assertEquals([], $provider->getListenersForEventType(get_class($nonExistentEvent)));
        $this->assertFalse($provider->hasListenersForEventType(get_class($nonExistentEvent)));
    }
}
