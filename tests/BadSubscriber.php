<?php

namespace Phariscope\Event\Tests;

use Phariscope\Event\EventAbstract;
use Phariscope\Event\EventSubscriber;

class BadSubscriber implements EventSubscriber
{
    public EventAbstract $domainEvent;

    public int $handleCallCount = 0;

    public function handle(EventAbstract $aDomainEvent): bool
    {
        throw new \Exception("I am a bad subscriber");
    }

    public function isSubscribedTo(EventAbstract $aDomainEvent): bool
    {
        return true;
    }
}
