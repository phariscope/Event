<?php

declare(strict_types=1);

namespace Phariscope\Event;

use Phariscope\Event\Psr14\Event;
use Phariscope\Event\Psr14\EventDispatcherInterface;
use Phariscope\Event\Psr14\ListenerInterface;
use Psr\Log\LoggerInterface;
use Psr\EventDispatcher\StoppableEventInterface;

class EventDispatcher implements EventDispatcherInterface
{
    /** @var array<int,ListenerInterface> $subscribers */
    protected array $subscribers;

    /** @var \SplQueue<Event> */
    protected \SplQueue $eventToDistribute;

    /**
     * Whether to distribute immediately after dispatch.
     * Kept public name to avoid BC break in serialized states; internal usages should rely on accessors.
     */
    protected bool $distributeImmediately = false;

    protected static ?EventDispatcher $instance = null;

    protected ?LoggerInterface $logger = null;

    private function __construct()
    {
        $this->subscribers = [];
        $this->eventToDistribute = new \SplQueue();
    }

    public static function instance(): EventDispatcher
    {
        if (null === static::$instance) {
            static::$instance = new EventDispatcher();
        };
        return static::$instance;
    }

    public static function tearDown(): void
    {
        static::$instance = null;
    }
    /**
     * @deprecated Use distributeImmediately() instead.
     */
    public function distributeImmmediatly(): void
    {
        // Keep backward compatibility with the misspelled method name
        $this->distributeImmediately();
    }

    /**
     * Enable immediate distribution of dispatched events.
     * When enabled, calls to dispatch() will trigger an automatic distribute().
     */
    public function distributeImmediately(): void
    {
        $this->distributeImmediately = true;
    }

    /**
     * Disable immediate distribution mode.
     */
    public function disableImmediateDistribution(): void
    {
        $this->distributeImmediately = false;
    }

    /**
     * Check whether immediate distribution is enabled.
     */
    public function isImmediateDistributionEnabled(): bool
    {
        return $this->distributeImmediately;
    }

    public function setLogger(?LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }

    public function __clone()
    {
        throw new \BadMethodCallException('Clone is not supported');
    }

    public function subscribe(
        ListenerInterface $eventSubscriber
    ): void {
        if (!$this->hasSubscriber($eventSubscriber)) {
            $this->subscribers[] = $eventSubscriber;
        }
    }

    public function hasSubscriber(ListenerInterface $subscriber): bool
    {
        return false !== array_search($subscriber, $this->subscribers, true);
    }

    /**
     * @deprecated Use dispatch() instead.
     * @param Event $anEvent
     */
    public function publish(Event $anEvent): void
    {
        $this->dispatch($anEvent);
    }

    /**
     * @return Event Return the event that was passed. Listeners MUST NOT modify the event.
     */

    public function dispatch(Event $event): Event
    {
        $this->eventToDistribute->enqueue($event);
        if ($this->distributeImmediately) {
            $this->distribute();
        }

        return $event;
    }

    public function distribute(): void
    {
        while (!$this->eventToDistribute->isEmpty()) {
            $event = $this->eventToDistribute->dequeue();
            $this->distributeEventToSubscribers($event);
        }
    }

    private function distributeEventToSubscribers(Event $event): void
    {
        foreach ($this->subscribers as $aSubscriber) {
            if ($aSubscriber->isSubscribedTo($event)) {
                $this->tryToHandleEvent($aSubscriber, $event);
                if ($event instanceof StoppableEventInterface && $event->isPropagationStopped()) {
                    break;
                }
            }
        }
    }

    private function tryToHandleEvent(ListenerInterface $subscriber, Event $event): void
    {
        try {
            $subscriber->handle($event);
        } catch (\Throwable $e) {
            if (null !== $this->logger) {
                $this->logger->error(
                    'Event listener threw an exception',
                    [
                        'exception' => $e,
                        'listener' => get_class($subscriber),
                        'event' => get_class($event),
                    ]
                );
            }
        }
    }

    public function unsubscribe(ListenerInterface $subscriber): void
    {
        foreach ($this->subscribers as $id => $aSubscriber) {
            if ($aSubscriber === $subscriber) {
                unset($this->subscribers[$id]);
            }
        }
    }

    /**
     * Remove all currently subscribed listeners.
     */
    public function clearSubscribers(): void
    {
        $this->subscribers = [];
    }

    /**
     * Get the number of currently queued events.
     */
    public function getQueuedEventCount(): int
    {
        return $this->eventToDistribute->count();
    }

    /**
     * Check if there are any events queued for distribution.
     */
    public function hasQueuedEvents(): bool
    {
        return !$this->eventToDistribute->isEmpty();
    }

    /**
     * Get all currently subscribed listeners.
     *
     * @return array<int,ListenerInterface>
     */
    public function getSubscribers(): array
    {
        return $this->subscribers;
    }

    /**
     * Get the number of subscribed listeners.
     */
    public function getSubscriberCount(): int
    {
        return count($this->subscribers);
    }
}
