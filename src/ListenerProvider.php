<?php

declare(strict_types=1);

namespace Phariscope\Event;

use Phariscope\Event\Psr14\Event;
use Phariscope\Event\Psr14\ListenerInterface;
use Phariscope\Event\Psr14\ListenerProviderInterface as LegacyListenerProviderInterface;

class ListenerProvider implements LegacyListenerProviderInterface
{
    /** @var array<string,array<int,ListenerInterface>> */
    private array $listeners = [];

    public function addListener(string $eventType, ListenerInterface $listener): void
    {
        $this->listeners[$eventType][] = $listener;
    }

    /**
     * @return array<int,ListenerInterface>
     */
    public function getListenersForEvent(Event $event): array
    {
        $eventType = get_class($event);
        return $this->listeners[$eventType] ?? [];
    }

    /**
     * Get listeners for a specific event type (more type-safe alternative).
     *
     * @param class-string<Event> $eventType
     * @return array<int,ListenerInterface>
     */
    public function getListenersForEventType(string $eventType): array
    {
        return $this->listeners[$eventType] ?? [];
    }

    /**
     * Check if there are any listeners registered for a specific event type.
     *
     * @param class-string<Event> $eventType
     */
    public function hasListenersForEventType(string $eventType): bool
    {
        return !empty($this->listeners[$eventType]);
    }

    // Note: PSR-14 support is provided by adapter classes; this provider remains legacy-compatible.
}
