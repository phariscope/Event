<?php

declare(strict_types=1);

namespace Phariscope\Event\Psr14;

/**
 * @package Phariscope\Event
 */
abstract class Event implements EventInterface
{
    public function __construct(protected \DateTimeImmutable $occurredOn = new \DateTimeImmutable())
    {
    }

    public function occurredOn(): \DateTimeImmutable
    {
        return $this->occurredOn;
    }
}
