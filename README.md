# Installation

```console
composer require phariscope/event
```

# Usage

Steps are:
* Create a domain event (name + past tense verb, example: `AccountCreated` extends `Phariscope\\Event\\Psr14\\Event`).
* Dispatch this event.
* Distribute events (manually, or enable immediate distribution).

Somewhere else:
* create a listener for an event (example: class `SendEmailWhenAccountCreatedListener` implements `Phariscope\\Event\\Psr14\\ListenerInterface`)
* register the listener; when the event is distributed the listener will handle it and do what it has to do

## Sample usage in an aggregate constructor
You SHOULD dispatch a domain event from an aggregate to signal its creation. This helps apply the Single Responsibility Principle: cross‑cutting concerns (emails, projections, integrations) live in listeners rather than inside the aggregate.

```php
<?php

namespace App\\Domain\\Account;

use Phariscope\\Event\\EventDispatcher;
use Phariscope\\Event\\Psr14\\Event;
use Phariscope\\Event\\Psr14\\ListenerInterface;

// 1) The domain event
final class AccountCreated extends Event
{
    public function __construct(
        public string $accountId,
        \DateTimeImmutable $occurredOn = new \DateTimeImmutable()
    ) {
        parent::__construct($occurredOn);
    }
}

// 2) The aggregate that emits the event
final class Account
{
    public function __construct(private string $id)
    {
        // ... domain invariants and state initialization ...

        // At the end of construction, dispatch the domain event
        EventDispatcher::instance()->dispatch(new AccountCreated($this->id));
    }
}

// 3) A listener that reacts to the event
final class SendWelcomeEmailListener implements ListenerInterface
{
    public function handle(Event $event): bool
    {
        if (!$event instanceof AccountCreated) {
            return false;
        }

        // send email here
        return true;
    }

    public function isSubscribedTo(Event $event): bool
    {
        return $event instanceof AccountCreated;
    }
}

// 4) Wiring the listener and triggering the flow
$dispatcher = EventDispatcher::instance();
$dispatcher->subscribe(new SendWelcomeEmailListener());

// Somewhere in your application flow
new Account('acc-123');

// Process the queued events (unless you enabled immediate distribution)
$dispatcher->distribute();
```

## Immediate distribution

By default, events dispatched via `EventDispatcher::dispatch()` are queued (FIFO) and processed when you call `EventDispatcher::distribute()`.

If you want events to be processed immediately upon dispatch, enable immediate distribution:

```php
use Phariscope\Event\EventDispatcher;

$dispatcher = EventDispatcher::instance();
$dispatcher->distributeImmediately(); // enables automatic distribute() after each dispatch
```

## Optional logging

You can plug a PSR-3 logger to observe listener exceptions without breaking the dispatch flow:

```php
use Phariscope\Event\EventDispatcher;
use Psr\Log\NullLogger; // or Monolog\Logger

$dispatcher = EventDispatcher::instance();
$dispatcher->setLogger(new NullLogger());
```

Deprecated: the misspelled method `distributeImmmediatly()` is still available for backward compatibility but will be removed in a future release. Use `distributeImmediately()` instead.

## Event immutability

Events in this library are treated as immutable messages. Listeners MUST NOT modify the event instance they receive. If you need to propagate additional information, dispatch a new event.

# To contribute to phariscope/Event

## Requirements

* docker
* git

## Install

* git clone git@github.com:phariscope/Event.git

## Unit test

```console
bin/phpunit
```

Using Test-Driven Development (TDD) principles (thanks to Kent Beck and others), following good practices (thanks to Uncle Bob and others) and the great book 'DDD in PHP' by C. Buenosvinos, C. Soronellas, K. Akbary

## Quality

* phpcs PSR12
* phpstan level 9
* coverage 100%
* infection MSI >99%

Quick check with:
```console
./codecheck
```

Check coverage with:
```console
bin/phpunit --coverage-html var
```
and view 'var/index.html' with your browser

Check infection with:
```console
bin/infection
```
and view 'var/infection.html' with your browser