<?php

declare(strict_types=1);

namespace Phariscope\Event\Psr\Adapters;

use Phariscope\Event\Psr14\Event as LegacyEvent;
use Phariscope\Event\Psr14\ListenerInterface as LegacyListener;

/**
 * Adapts a legacy Phariscope ListenerInterface into a PSR-14 callable(object):void.
 */
final class ListenerCallableAdapter
{
    public static function adapt(LegacyListener $listener): callable
    {
        return static function (object $event) use ($listener): void {
            if ($event instanceof LegacyEvent && $listener->isSubscribedTo($event)) {
                $listener->handle($event);
            }
        };
    }
}
