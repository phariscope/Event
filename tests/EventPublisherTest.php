<?php

namespace Phariscope\Event\Tests;

use Phariscope\Event\EventPublisher;
use Phariscope\Event\Tools\SpySubscriber;
use PHPUnit\Framework\TestCase;

class EventPublisherTest extends TestCase
{
    protected function setUp(): void
    {
        EventPublisher::tearDown();
    }

    public function testHasSubscribed(): void
    {
        $subscriber = new SpySubscriber();

        EventPublisher::instance()->subscribe($subscriber);
        $this->assertTrue(EventPublisher::instance()->hasSubscriber($subscriber));

        EventPublisher::instance()->subscribe($subscriber);
        $this->assertTrue(EventPublisher::instance()->hasSubscriber($subscriber));
    }

    public function testPublishWithDistributeImmediatly(): void
    {
        EventPublisher::instance()->distributeImmmediatly();

        $subscriber = new SpySubscriber();
        EventPublisher::instance()->subscribe($subscriber);

        $event = new EventSent("unId");
        EventPublisher::instance()->publish($event);

        $this->assertInstanceOf(EventSent::class, $subscriber->domainEvent);
        /** @var EventSent $es */
        $es = $subscriber->domainEvent;
        $this->assertEquals("unId", $es->id());
    }

    public function testUnsubscribe(): void
    {
        $subscriber = new SpySubscriber();
        EventPublisher::instance()->subscribe($subscriber);
        EventPublisher::instance()->unsubscribe($subscriber);
        $event = new EventSent("unId");
        EventPublisher::instance()->publish($event);
        $this->assertFalse(isset($subscriber->domainEvent));
    }

    public function testExceptionOnCloneAttempt(): void
    {
        $this->expectException(\BadMethodCallException::class);
        $publisher = EventPublisher::instance();
        $leClone = clone $publisher;
    }

    public function testCreateNotInitializedInstance(): void
    {
        $subscriber = new SpySubscriber();

        EventPublisher::instance()->subscribe($subscriber);

        $this->assertTrue(EventPublisher::instance()->hasSubscriber($subscriber));

        EventPublisher::tearDown();

        $subscriber = new SpySubscriber();

        EventPublisher::instance()->subscribe($subscriber);

        $this->assertTrue(EventPublisher::instance()->hasSubscriber($subscriber));
    }

    public function testDistributeOneEvent(): void
    {
        $event = new EventSent("unId");

        $eventSubscriber = new SpySubscriber();
        EventPublisher::instance()->subscribe($eventSubscriber);

        EventPublisher::instance()->publish($event);

        $this->assertFalse(isset($eventSubscriber->domainEvent));

        EventPublisher::instance()->distribute();

        $this->assertInstanceOf(EventSent::class, $eventSubscriber->domainEvent);
    }

    public function testDistributeTreeEvents(): void
    {
        $event1 = new EventSent("unId");
        $event2 = new EventSent("id2");
        $event3 = new EventSent("id3");

        $eventSubscriber = new SpySubscriber();
        EventPublisher::instance()->subscribe($eventSubscriber);

        EventPublisher::instance()->publish($event1);
        EventPublisher::instance()->publish($event2);
        EventPublisher::instance()->publish($event3);

        EventPublisher::instance()->distribute();

        $this->assertEquals(3, $eventSubscriber->handleCallCount);
        $this->assertEquals($event1, $eventSubscriber->traces[0]);
        $this->assertEquals($event2, $eventSubscriber->traces[1]);
        $this->assertEquals($event3, $eventSubscriber->traces[2]);

        EventPublisher::instance()->distribute();
        $this->assertEquals(3, $eventSubscriber->handleCallCount);
    }

    public function testDistributionIdempotent(): void
    {
        $event = new EventSent("unId");

        $eventSubscriber = new SpySubscriber();
        EventPublisher::instance()->subscribe($eventSubscriber);

        EventPublisher::instance()->distributeImmmediatly();

        EventPublisher::instance()->publish($event);
        EventPublisher::instance()->distribute();

        $this->assertEquals(1, $eventSubscriber->handleCallCount);
    }

    public function testABadSubscriberMustNotStopDistribute(): void
    {
        EventPublisher::instance()->distributeImmmediatly();

        $event = new EventSent("unId");
        $event2 = new EventSent("unId2");

        $badSubscriber = new BadSubscriber();
        EventPublisher::instance()->subscribe($badSubscriber);

        $eventSubscriber = new SpySubscriber();
        EventPublisher::instance()->subscribe($eventSubscriber);

        EventPublisher::instance()->publish($event);
        EventPublisher::instance()->publish($event2);
        EventPublisher::instance()->distribute();

        $this->assertEquals(2, $eventSubscriber->handleCallCount);
    }

    public function testSubscribeUnsubscribe(): void
    {
        $subscriber = new SpySubscriber();
        $publisher = EventPublisher::instance();
        $publisher->subscribe($subscriber);
        $publisher->subscribe($subscriber);

        $this->assertTrue($publisher->hasSubscriber($subscriber));

        $publisher->unsubscribe($subscriber);

        $this->assertFalse($publisher->hasSubscriber($subscriber));
    }
}
