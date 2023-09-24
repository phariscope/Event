<?php

namespace Phariscope\Event\Tests;

use Phariscope\Event\EventInterface;
use Phariscope\Event\EventSubscriber;

/**
 * cet espion permettra de simplifer vos tests de publication de vos évènements
 */
class SpySubscriber implements EventSubscriber
{
    public EventInterface $domainEvent;

    public int $handleCallCount = 0;

    /** @var array<EventInterface> */
    public array $traces;

    public function handle(EventInterface $aDomainEvent): bool
    {
        $this->domainEvent = $aDomainEvent;
        $this->handleCallCount++;
        $this->traces[] = $aDomainEvent;
        return true;
    }

    public function isSubscribedTo(EventInterface $aDomainEvent): bool
    {
        return true;
    }
}
