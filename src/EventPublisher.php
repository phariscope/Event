<?php

namespace Phariscope\Event;

/**
 * @deprecated Use EventDispatcher instead.
 * @package Phariscope\Event
 */
final class EventPublisher extends EventDispatcher
{
    private static ?EventPublisher $instance = null;

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

    public static function tearDown(): void
    {
        static::$instance = null;
    }
}
