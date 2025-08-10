<?php

namespace Phariscope\Event\Tests;

use Phariscope\Event\EventDispatcher;
use Phariscope\Event\Psr14\Event;
use Phariscope\Event\Psr14\ListenerInterface;
use Phariscope\Event\Tests\Tools\SpyLogger;
use PHPUnit\Framework\TestCase;
use Phariscope\Event\Tools\SpyListener;

final class LoggerTest extends TestCase
{
    protected function setUp(): void
    {
        EventDispatcher::tearDown();
    }

    public function testListenerExceptionIsLogged(): void
    {
        // Arrange
        $logger = new SpyLogger();

        $dispatcher = EventDispatcher::instance();
        $dispatcher->setLogger($logger);
        $dispatcher->distributeImmediately();

        $dispatcher->subscribe(new class implements ListenerInterface {
            public function handle(Event $event): bool
            {
                throw new \RuntimeException('boom');
            }
            public function isSubscribedTo(Event $event): bool
            {
                return true;
            }
        });

        // Act
        $dispatcher->dispatch(new class extends Event {
        });

        // Assert
        $this->assertGreaterThanOrEqual(1, $logger->countLevel('error'));
        $record = $logger->lastRecord();
        $this->assertNotNull($record);
        $this->assertEquals('error', $record['level']);
        $this->assertArrayHasKey('exception', $record['context']);
        $this->assertArrayHasKey('listener', $record['context']);
        $this->assertArrayHasKey('event', $record['context']);
    }

    public function testListenerExceptionWithoutLoggerDoesNotStopOthers(): void
    {
        // Arrange: no logger set
        $dispatcher = EventDispatcher::instance();
        $dispatcher->distributeImmediately();

        $bad = new class implements ListenerInterface {
            public function handle(Event $event): bool
            {
                throw new \RuntimeException('no logger boom');
            }
            public function isSubscribedTo(Event $event): bool
            {
                return true;
            }
        };

        $spy = new SpyListener();

        $dispatcher->subscribe($bad);
        $dispatcher->subscribe($spy);

        // Act: should not throw
        $dispatcher->dispatch(new class extends Event {
        });

        // Assert: spy still handled the event
        $this->assertEquals(1, $spy->handleCallCount);
    }
}
