<?php

declare(strict_types=1);

namespace Phariscope\Event\Tests;

use Phariscope\Event\Psr14\Event as LegacyEvent;
use Phariscope\Event\Psr14\ListenerInterface;
use Psr\EventDispatcher\StoppableEventInterface;

/**
 * Test listener that tracks calls and can optionally stop event propagation.
 *
 * This class is designed for testing purposes to verify event dispatching behavior,
 * listener execution order, and propagation stopping functionality.
 */
final class TestListener implements ListenerInterface
{
    /** @var list<string> */
    private array $calls = [];

    public function __construct(
        private string $identifier,
        private bool $shouldStopPropagation = false
    ) {
    }

    public function handle(LegacyEvent $event): bool
    {
        $this->calls[] = $this->identifier;

        if ($this->shouldStopPropagation && $event instanceof StoppableEventInterface) {
            if (method_exists($event, 'stop')) {
                $event->stop();
            }
        }

        return true;
    }

    public function isSubscribedTo(LegacyEvent $event): bool
    {
        return true;
    }

    /**
     * Get all calls made to this listener.
     *
     * @return list<string> Array of identifier strings representing each call
     */
    public function getCalls(): array
    {
        return $this->calls;
    }

    /**
     * Get the number of times this listener was called.
     */
    public function getCallCount(): int
    {
        return count($this->calls);
    }

    /**
     * Check if this listener was called at least once.
     */
    public function wasCalled(): bool
    {
        return !empty($this->calls);
    }

    /**
     * Reset the call history.
     */
    public function resetCalls(): void
    {
        $this->calls = [];
    }

    /**
     * Get the identifier of this listener.
     */
    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    /**
     * Check if this listener is configured to stop propagation.
     */
    public function shouldStopPropagation(): bool
    {
        return $this->shouldStopPropagation;
    }
}
