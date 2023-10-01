<?php

namespace Phariscope\Event;

abstract class EventAbstract implements EventInterface
{
    public function __construct(protected \DateTimeImmutable $occurredOn = new \DateTimeImmutable())
    {
    }

    public function occurredOn(): \DateTimeImmutable
    {
        return $this->occurredOn;
    }
}
