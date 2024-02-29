<?php

namespace Phariscope\Event\Psr14;

interface EventInterface
{
    public function occurredOn(): \DateTimeImmutable;
}
