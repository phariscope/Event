<?php

namespace Phariscope\Event\Tests\Tools;

use Phariscope\Event\Tests\EventSent;
use Phariscope\Event\Tools\SpySubscriber;
use PHPUnit\Framework\TestCase;

class SpySubscriberTest extends TestCase
{
    public function testHandle(): void
    {
        $event = new EventSent("1");
        $spy = new SpySubscriber();
        $this->assertTrue($spy->handle($event));
    }
}
