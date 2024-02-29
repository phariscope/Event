<?php

namespace Phariscope\Event\Psr14;

use Phariscope\Event\Psr14\Event;

/**
 * Mapper from an event to the listeners that are applicable to that event.
 */
interface ListenerProviderInterface
{
    /**
     * @param Event $event
     *   An event for which to return the relevant listeners.
     * @return array<ListenerInterface>
     *   An iterable (array, iterator, or generator) of callables.  Each
     *   callable MUST be type-compatible with $event.
     */
    public function getListenersForEvent(Event $event): array;
}
