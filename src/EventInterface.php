<?php

namespace Phariscope\Event;

interface EventInterface
{
    public function occurredOn(): \DateTimeImmutable;
}
