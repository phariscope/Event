<?php

namespace Phariscope\Event\Tests\SomeListeners;

use Phariscope\Event\EventDispatcher;
use Phariscope\Event\Psr14\Event;
use Phariscope\Event\Psr14\ListenerInterface;
use Phariscope\Event\Tests\EventSent;
use Phariscope\Event\Tests\EventSent2;

class CallAnotherEventListener implements ListenerInterface
{
    /**
     * @return bool handled event. true si l'evenement a été traité.
     */
    public function handle(Event $aDomainEvent): bool
    {
        EventDispatcher::instance()->dispatch(new EventSent2("2"));
        return true;
    }

    public function isSubscribedTo(Event $aDomainEvent): bool
    {
        return $aDomainEvent instanceof EventSent;
    }
}
