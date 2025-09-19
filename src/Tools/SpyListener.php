<?php

declare(strict_types=1);

namespace Phariscope\Event\Tools;

use Phariscope\Event\Psr14\Event;
use Phariscope\Event\Psr14\ListenerInterface;

/**
 * a spy subscriber for testing purpose
 */
class SpyListener implements ListenerInterface
{
    public ?Event $domainEvent = null;

    public int $handleCallCount = 0;

    /** @var array<int,Event> */
    public array $traces = [];

    public function handle(Event $event): bool
    {
        $this->domainEvent = $event;
        $this->handleCallCount++;
        $this->traces[] = $event;
        return true;
    }

    public function isSubscribedTo(Event $event): bool
    {
        return true;
    }
}
