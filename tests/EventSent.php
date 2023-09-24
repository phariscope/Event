<?php

namespace Phariscope\Event\Tests;

use Phariscope\Event\EventAbstract;
use Safe\DateTimeImmutable;

/**
 * EventSended : nom + verbe au passé pour nommer vos evennements
 */
class EventSent extends EventAbstract
{
    private string $id;
    private DateTimeImmutable $occurredOn;

    public function __construct(string $id, DateTimeImmutable $occuredOn = new DateTimeImmutable())
    {
        $this->id = $id;
        $this->occurredOn = $occuredOn;
    }

    public function id(): string
    {
        return $this->id;
    }

    public function occurredOn(): DateTimeImmutable
    {
        return $this->occurredOn;
    }
}
