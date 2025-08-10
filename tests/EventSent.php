<?php

declare(strict_types=1);

namespace Phariscope\Event\Tests;

use Phariscope\Event\Psr14\Event;

/**
 * EventSent : name + past tensed verb
 */
class EventSent extends Event
{
    private string $id;

    public function __construct(string $id, \DateTimeImmutable $occurredOn = new \DateTimeImmutable())
    {
        parent::__construct($occurredOn);
        $this->id = $id;
    }

    public function id(): string
    {
        return $this->id;
    }
}
