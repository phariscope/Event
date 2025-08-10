<?php

declare(strict_types=1);

namespace Phariscope\Event;

use Phariscope\Event\Psr14\ListenerInterface;

/** @deprecated use ListenerInterface */
interface EventSubscriber extends ListenerInterface
{
}
