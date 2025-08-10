<?php

declare(strict_types=1);

namespace Phariscope\Event\Tests\Psr;

use Phariscope\Event\Psr\Adapters\ListenerCallableAdapter;
use Phariscope\Event\Psr14\Event;
use Phariscope\Event\Psr14\ListenerInterface;
use PHPUnit\Framework\TestCase;

final class AdapterTest extends TestCase
{
    public function testLegacyListenerAdapterCallsOnlyWhenSubscribed(): void
    {
        // Arrange
        $trace = [];
        $legacy = new class implements ListenerInterface {
            /** @var list<string> */
            public array $trace = [];
            public function handle(Event $event): bool
            {
                $this->trace[] = 'handled';
                return true;
            }
            public function isSubscribedTo(Event $event): bool
            {
                return true;
            }
        };

        // Act
        $callable = ListenerCallableAdapter::adapt($legacy);
        $event = new class extends Event {
        };
        $callable($event);

        // Assert
        $this->assertSame(['handled'], $legacy->trace);

        // Act: should not call the listener
        $legacy->trace = [];
        $callable(new \stdClass());

        // Assert
        $this->assertSame([], $legacy->trace);
    }
}
