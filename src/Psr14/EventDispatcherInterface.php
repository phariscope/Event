<?php

namespace Phariscope\Event\Psr14;

use Phariscope\Event\Psr14\Event;

/**
 * Defines a dispatcher for events.
 */
interface EventDispatcherInterface
{
    /**
     * Provide all relevant listeners with an event to process.
     *
     * @param Event $event
     *   The object to process.
     *
     * @return Event
     *   The Event that was passed, now modified by listeners.
     */
    public function dispatch(Event $event): Event;
}
