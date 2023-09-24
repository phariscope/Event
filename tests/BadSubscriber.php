<?php

namespace Phariscope\Event\Tests;

use Phariscope\Event\EventInterface;
use Phariscope\Event\EventSubscriber;

class BadSubscriber implements EventSubscriber
{
    public EventInterface $domainEvent;

    public int $handleCallCount = 0;

    public function handle(EventInterface $aDomainEvent): bool
    {
        throw new \Exception("I am a bad subscriber");
    }

    public function isSubscribedTo(EventInterface $aDomainEvent): bool
    {
        return true;
    }
}
