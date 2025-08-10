<?php

declare(strict_types=1);

namespace Phariscope\Event\Psr14;

interface ListenerInterface
{
    /**
     * @return bool True if the event was handled successfully, false otherwise.
     */
    public function handle(Event $event): bool;

    public function isSubscribedTo(Event $event): bool;
}
