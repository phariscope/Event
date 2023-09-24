<?php

namespace Phariscope\Event;

use Safe\DateTimeImmutable;

abstract class EventAbstract implements EventInterface
{
    public function __construct(private DateTimeImmutable $occurredOn)
    {
    }

    public function occurredOn(): DateTimeImmutable
    {
        return $this->occurredOn;
    }
}
