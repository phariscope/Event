<?php

declare(strict_types=1);

namespace Phariscope\Event\Tests;

use Phariscope\Event\Psr14\Event;
use Phariscope\Event\Psr14\ListenerInterface;

class BadSubscriber implements ListenerInterface
{
    public Event $domainEvent;

    public int $handleCallCount = 0;

    public function handle(Event $aDomainEvent): bool
    {
        throw new \Exception("I am a bad subscriber");
    }

    public function isSubscribedTo(Event $aDomainEvent): bool
    {
        return true;
    }
}
