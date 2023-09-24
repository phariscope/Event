<?php

namespace Phariscope\Event;

interface EventSubscriber
{
    /**
     * @return bool handled event. true si l'evenement a été traité.
     */
    public function handle(EventAbstract $aDomainEvent): bool;

    public function isSubscribedTo(EventAbstract $aDomainEvent): bool;
}
