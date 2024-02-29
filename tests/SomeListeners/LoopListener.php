<?php

namespace Phariscope\Event\Tests\SomeListeners;

use Phariscope\Event\EventDispatcher;
use Phariscope\Event\Psr14\Event;
use Phariscope\Event\Tests\EventSent;
use Phariscope\Event\Tools\SpyListener;

class LoopListener extends SpyListener
{
    public function handle(Event $aDomainEvent): bool
    {
        parent::handle($aDomainEvent);
        $event = new EventSent("2");

        EventDispatcher::instance()->dispatch($event);
        return true;
    }

    public function isSubscribedTo(Event $aDomainEvent): bool
    {
        return true;
    }
}
