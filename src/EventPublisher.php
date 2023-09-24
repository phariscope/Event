<?php

namespace Phariscope\Event;

final class EventPublisher
{
    /** @var array<int,EventSubscriber> $subscribers */
    private array $subscribers;
    private static ?EventPublisher $instance = null;

    /** @var array<EventInterface> */
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

    /**
     * @return int index du subscriber
     */
    public function subscribe(
        EventSubscriber $aDomainEventSubscriber
    ): int {
        $this->subscribers[] = $aDomainEventSubscriber;
        return count($this->subscribers) - 1;
    }

    public function publish(EventInterface $anEvent): void
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

    private function distributeEventToSubscribers(EventInterface $event): void
    {
        foreach ($this->subscribers as $aSubscriber) {
            $this->tryToHandleEventIfSubscribed($aSubscriber, $event);
        }
    }

    private function tryToHandleEventIfSubscribed(EventSubscriber $subscriber, EventInterface $event): void
    {
        if ($subscriber->isSubscribedTo($event)) {
            $this->tryToHandleEvent($subscriber, $event);
        }
    }

    private function tryToHandleEvent(EventSubscriber $subscriber, EventInterface $event): void
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

    /**
     * @param int $id index du subscriber
     */
    public function unsubscribe(int $id): void
    {
        unset($this->subscribers[$id]);
    }

    public static function tearDown(): void
    {
        static::$instance = null;
    }
}
