<?php

declare(strict_types=1);

namespace Phariscope\Event\Psr14;

interface EventInterface
{
    public function occurredOn(): \DateTimeImmutable;
}
