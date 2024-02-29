<?php

namespace Phariscope\Event;

use Phariscope\Event\Psr14\Event;
use Phariscope\Event\Psr14\EventDispatcherInterface;
use Phariscope\Event\Psr14\ListenerInterface;

class EventDispatcher implements EventDispatcherInterface
{
    /** @var array<int,ListenerInterface> $subscribers */
    protected array $subscribers;

    /** @var array<Event> */
    protected array $eventToDistribute;

    protected bool $distributeImmediatly = false;

    protected static ?EventDispatcher $instance = null;

    private function __construct()
    {
        $this->subscribers = [];
        $this->eventToDistribute = [];
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
    public function distributeImmmediatly(): void
    {
        $this->distributeImmediatly = true;
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
     * @return void
     */
    public function publish(Event $anEvent): void
    {
        $this->dispatch($anEvent);
    }

    /**
     * @return Event return the event that was passed. In our implementation, event MUST NOT be modified by listener.
     */

    public function dispatch(Event $event): Event
    {
        array_push($this->eventToDistribute, $event);
        if ($this->distributeImmediatly) {
            $this->distribute();
        }

        return $event;
    }

    public function distribute(): void
    {
        if (count($this->eventToDistribute) == 0) {
            return;
        }
        while ($event = array_shift($this->eventToDistribute)) {
            $this->distributeEventToSubscribers($event);
        }
    }

    private function distributeEventToSubscribers(Event $event): void
    {
        foreach ($this->subscribers as $aSubscriber) {
            $this->tryToHandleEventIfSubscribed($aSubscriber, $event);
        }
    }

    private function tryToHandleEventIfSubscribed(ListenerInterface $subscriber, Event $event): void
    {
        if ($subscriber->isSubscribedTo($event)) {
            $this->tryToHandleEvent($subscriber, $event);
        }
    }

    private function tryToHandleEvent(ListenerInterface $subscriber, Event $event): void
    {
        try {
            $subscriber->handle($event);
        } catch (\Exception $e) {
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
}
