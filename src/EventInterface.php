<?php

namespace Phariscope\Event;

use Safe\DateTimeImmutable;

interface EventInterface
{
    public function occurredOn(): DateTimeImmutable;
}
