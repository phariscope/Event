<?php

declare(strict_types=1);

namespace Phariscope\Event\Tests;

use Phariscope\Event\Psr14\Event as LegacyEvent;
use Psr\EventDispatcher\StoppableEventInterface;

/**
 * Test event that implements StoppableEventInterface.
 *
 * This class is designed for testing event propagation stopping functionality.
 * It can be configured to allow or disallow stopping, which is useful for
 * testing different scenarios in event dispatching.
 */
final class TestStoppableEvent extends LegacyEvent implements StoppableEventInterface
{
    private bool $stopped = false;

    public function __construct(
        private bool $allowStopping = true,
        ?\DateTimeImmutable $occurredOn = null
    ) {
        parent::__construct($occurredOn ?? new \DateTimeImmutable());
    }

    /**
     * Stop the event propagation.
     *
     * Only works if stopping is allowed (configured in constructor).
     */
    public function stop(): void
    {
        if ($this->allowStopping) {
            $this->stopped = true;
        }
    }

    /**
     * Check if the event propagation has been stopped.
     */
    public function isPropagationStopped(): bool
    {
        return $this->stopped;
    }

    /**
     * Check if this event allows stopping.
     */
    public function allowsStopping(): bool
    {
        return $this->allowStopping;
    }

    /**
     * Force stop the event regardless of the allowStopping setting.
     *
     * This method is useful for testing edge cases.
     */
    public function forceStop(): void
    {
        $this->stopped = true;
    }

    /**
     * Reset the stopped state.
     *
     * This method is useful for reusing the same event instance in tests.
     */
    public function reset(): void
    {
        $this->stopped = false;
    }

    /**
     * Create a stoppable event that allows stopping.
     */
    public static function createStoppable(?\DateTimeImmutable $occurredOn = null): self
    {
        return new self(allowStopping: true, occurredOn: $occurredOn);
    }

    /**
     * Create a stoppable event that does not allow stopping.
     */
    public static function createNonStoppable(?\DateTimeImmutable $occurredOn = null): self
    {
        return new self(allowStopping: false, occurredOn: $occurredOn);
    }
}
