# Installation

```console
composer require phariscope/event
```

# Usage

Steps are:
* Create a domain event (name + past tense verb, example: `AccountCreated` extends `EventAbstract`).
* Publish this event.
* Distribute events.

Somewhere else:
* create a subscriber at an event (exemple: class SendEmailWhenAccountCreatedSubscriber implements EventSubscriber)
* register the subscriber, when event will be distrute the subscriber will handle it and do what it has to do

# To Contribut to pharsicope/Event

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