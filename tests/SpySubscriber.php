<?php

namespace Phariscope\Event\Tests;

use Phariscope\Event\EventAbstract;
use Phariscope\Event\EventSubscriber;

/**
 * cet espion permettra de simplifer vos tests de publication de vos évènements
 */
class SpySubscriber implements EventSubscriber
{
    public EventAbstract $domainEvent;

    public int $handleCallCount = 0;

    /** @var array<EventAbstract> */
    public array $traces;

    public function handle(EventAbstract $aDomainEvent): bool
    {
        $this->domainEvent = $aDomainEvent;
        $this->handleCallCount++;
        $this->traces[] = $aDomainEvent;
        return true;
    }

    public function isSubscribedTo(EventAbstract $aDomainEvent): bool
    {
        return true;
    }
}
