<?php

namespace Phariscope\Event;

use Phariscope\Event\Psr14\Event;
use Phariscope\Event\Psr14\ListenerInterface;
use Phariscope\Event\Psr14\ListenerProviderInterface;

class ListenerProvider implements ListenerProviderInterface
{
    /** @var array<string,array<ListenerInterface>> */
    private array $listeners = [];

    public function addListener(string $eventType, ListenerInterface $listener): void
    {
        $this->listeners[$eventType][] = $listener;
    }

    /**
     * @return array<ListenerInterface>
     */
    public function getListenersForEvent(Event $event): array
    {
        $eventType = get_class($event);
        return $this->listeners[$eventType] ?? [];
    }
}
