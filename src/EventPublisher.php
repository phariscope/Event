<?php

namespace Phariscope\Event;

final class EventPublisher
{
    /** @var array<int,EventSubscriber> $subscribers */
    private array $subscribers;
    private static ?EventPublisher $instance = null;

    /** @var array<EventAbstract> */
    private array $eventToDistribute;

    private bool $distributeImmediatly = false;


    private function __construct()
    {
        $this->subscribers = [];
        $this->eventToDistribute = [];
    }

    public static function instance(): EventPublisher
    {
        if (null === static::$instance) {
            static::$instance = new EventPublisher();
        };
        return static::$instance;
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
        EventSubscriber $eventSubscriber
    ): void {
        if (!$this->hasSubscriber($eventSubscriber)) {
            $this->subscribers[] = $eventSubscriber;
        }
    }

    public function publish(EventAbstract $anEvent): void
    {
        $this->eventToDistribute[] =  $anEvent;
        if ($this->distributeImmediatly) {
            $this->distribute();
        }
    }

    public function distribute(): void
    {
        foreach ($this->eventToDistribute as $index => $event) {
            $this->distributeEventToSubscribers($event);
            $this->unsetEventByIndex($index);
        }
    }

    private function distributeEventToSubscribers(EventAbstract $event): void
    {
        foreach ($this->subscribers as $aSubscriber) {
            $this->tryToHandleEventIfSubscribed($aSubscriber, $event);
        }
    }

    private function tryToHandleEventIfSubscribed(EventSubscriber $subscriber, EventAbstract $event): void
    {
        if ($subscriber->isSubscribedTo($event)) {
            $this->tryToHandleEvent($subscriber, $event);
        }
    }

    private function tryToHandleEvent(EventSubscriber $subscriber, EventAbstract $event): void
    {
        try {
            $subscriber->handle($event);
        } catch (\Exception $e) {
        }
    }

    private function unsetEventByIndex(int $index): void
    {
        unset($this->eventToDistribute[$index]);
    }

    public function unsubscribe(EventSubscriber $subscriber): void
    {
        foreach ($this->subscribers as $id => $aSubscriber) {
            if ($aSubscriber === $subscriber) {
                unset($this->subscribers[$id]);
            }
        }
    }

    public static function tearDown(): void
    {
        static::$instance = null;
    }

    public function hasSubscriber(EventSubscriber $subscriber): bool
    {
        return false !== array_search($subscriber, $this->subscribers, true);
    }
}
