<?php

namespace Phariscope\Event\Psr14;

interface ListenerInterface
{
    /**
     * @return bool handled event. true si l'evenement a été traité.
     */
    public function handle(Event $aDomainEvent): bool;

    public function isSubscribedTo(Event $aDomainEvent): bool;
}
