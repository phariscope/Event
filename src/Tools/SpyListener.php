<?php

namespace Phariscope\Event\Tools;

use Phariscope\Event\Psr14\Event;
use Phariscope\Event\Psr14\ListenerInterface;

/**
 * a spy subscriber for testing purpose
 */
class SpyListener implements ListenerInterface
{
    public Event $domainEvent;

    public int $handleCallCount = 0;

    /** @var array<Event> */
    public array $traces;

    public function handle(Event $aDomainEvent): bool
    {
        $this->domainEvent = $aDomainEvent;
        $this->handleCallCount++;
        $this->traces[] = $aDomainEvent;
        return true;
    }

    public function isSubscribedTo(Event $aDomainEvent): bool
    {
        return true;
    }
}
