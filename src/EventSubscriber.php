<?php

namespace Phariscope\Event;

interface EventSubscriber
{
    /**
     * @return bool handled event. true si l'evenement a été traité.
     */
    public function handle(EventInterface $aDomainEvent): bool;

    public function isSubscribedTo(EventInterface $aDomainEvent): bool;
}
