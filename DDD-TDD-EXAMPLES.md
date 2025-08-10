# DDD/TDD Examples with phariscope/event

This document presents concrete examples of using the `phariscope/event` library in a Domain-Driven Design (DDD) context, focusing on event management.

## Layered Architecture

```
Infrastructure Layer (Controllers, Routes)
    ↓
Application Layer (Services, Use Cases)  
    ↓
Domain Layer (Aggregates, Events, Value Objects)
```

---

## Domain Layer - Account Aggregate

### 1. The Domain Event

```php
<?php

namespace App\Domain\Account\Event;

use Phariscope\Event\Psr14\Event;

final class AccountCreated extends Event
{
    public function __construct(
        public readonly string $accountId,
        public readonly string $emailOwner,
        public readonly string $accountName,
        \DateTimeImmutable $occurredOn = new \DateTimeImmutable()
    ) {
        parent::__construct($occurredOn);
    }
}
```

### 2. The Account Aggregate

```php
<?php

namespace App\Domain\Account;

use App\Domain\Account\Event\AccountCreated;
use Phariscope\Event\EventDispatcher;

final class Account
{
    private function __construct(
        private readonly string $id,
        private readonly string $emailOwner,
        private readonly string $name
    ) {
    }

    public static function create(
        string $id,
        string $emailOwner,
        string $name
    ): self {
        // Business rules validation
        if (empty($id) || empty($emailOwner) || empty($name)) {
            throw new \InvalidArgumentException('Account data cannot be empty');
        }

        if (!filter_var($emailOwner, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('Invalid email format');
        }

        $account = new self($id, $emailOwner, $name);

        // 🎯 DISPATCH THE DOMAIN EVENT
        EventDispatcher::instance()->dispatch(
            new AccountCreated($id, $emailOwner, $name)
        );

        return $account;
    }

    public function id(): string
    {
        return $this->id;
    }

    public function emailOwner(): string
    {
        return $this->emailOwner;
    }

    public function name(): string
    {
        return $this->name;
    }
}
```

### 3. Event Testing

```php
<?php

namespace App\Tests\Domain\Account;

use App\Domain\Account\Account;
use App\Domain\Account\Event\AccountCreated;
use Phariscope\Event\EventDispatcher;
use Phariscope\Event\Tools\SpyListener;
use PHPUnit\Framework\TestCase;

final class AccountEventTest extends TestCase
{
    private SpyListener $eventSpy;
    private EventDispatcher $dispatcher;

    protected function setUp(): void
    {
        // Reset dispatcher for each test
        EventDispatcher::tearDown();
        $this->dispatcher = EventDispatcher::instance();
        
        // 🕵️ SPY TO CAPTURE EVENTS
        $this->eventSpy = new SpyListener();
        $this->dispatcher->subscribe($this->eventSpy);
    }

    public function testAccountCreationDispatchesEvent(): void
    {
        // Arrange
        $accountId = 'acc-123';
        $email = 'john.doe@example.com';
        $name = 'John Doe Account';

        // Act
        Account::create($accountId, $email, $name);
        
        // 🎯 DISTRIBUTE EVENTS
        $this->dispatcher->distribute();

        // Assert - The event was dispatched
        $this->assertEquals(1, $this->eventSpy->handleCallCount);
        $this->assertInstanceOf(AccountCreated::class, $this->eventSpy->domainEvent);
        
        /** @var AccountCreated $event */
        $event = $this->eventSpy->domainEvent;
        $this->assertEquals($accountId, $event->accountId);
        $this->assertEquals($email, $event->emailOwner);
        $this->assertEquals($name, $event->accountName);
    }
}
```

---

## Application Layer - Creation Service

### 1. Application Service

```php
<?php

namespace App\Application\Account;

use App\Domain\Account\Account;
use App\Infrastructure\Account\AccountRepositoryInMemory;
use Phariscope\Event\EventDispatcher;

final class CreateAccountService
{
    public function __construct(
        private readonly AccountRepositoryInMemory $accountRepository,
        private readonly EventDispatcher $eventDispatcher
    ) {
    }

    public function execute(CreateAccountCommand $command): Account
    {
        // Check that the account doesn't already exist
        if ($this->accountRepository->exists($command->accountId)) {
            throw new \DomainException("Account with ID {$command->accountId} already exists");
        }

        // 🎯 CREATE THE AGGREGATE (which automatically dispatches the event)
        $account = Account::create(
            $command->accountId,
            $command->emailOwner,
            $command->accountName
        );

        // Persistence
        $this->accountRepository->add($account);

        // 🎯 DISTRIBUTE EVENTS
        $this->eventDispatcher->distribute();

        return $account;
    }
}
```

### 2. Command DTO

```php
<?php

namespace App\Application\Account;

final class CreateAccountCommand
{
    public function __construct(
        public readonly string $accountId,
        public readonly string $emailOwner,
        public readonly string $accountName
    ) {
    }
}
```

### 3. Service Testing - Focus on Events

```php
<?php

namespace App\Tests\Application\Account;

use App\Application\Account\CreateAccountCommand;
use App\Application\Account\CreateAccountService;
use App\Domain\Account\Event\AccountCreated;
use App\Infrastructure\Account\AccountRepositoryInMemory;
use Phariscope\Event\EventDispatcher;
use Phariscope\Event\Tools\SpyListener;
use PHPUnit\Framework\TestCase;

final class CreateAccountServiceEventTest extends TestCase
{
    private AccountRepositoryInMemory $repository;
    private EventDispatcher $dispatcher;
    private SpyListener $eventSpy;
    private CreateAccountService $service;

    protected function setUp(): void
    {
        // Reset dispatcher
        EventDispatcher::tearDown();
        $this->dispatcher = EventDispatcher::instance();
        
        // In-memory repository (no mock)
        $this->repository = new AccountRepositoryInMemory();
        
        // 🕵️ SPY FOR EVENTS
        $this->eventSpy = new SpyListener();
        $this->dispatcher->subscribe($this->eventSpy);
        
        // Service under test
        $this->service = new CreateAccountService(
            $this->repository,
            $this->dispatcher
        );
    }

    public function testServiceDispatchesAccountCreatedEvent(): void
    {
        // Arrange
        $command = new CreateAccountCommand(
            'acc-service-test',
            'service@example.com',
            'Service Test Account'
        );

        // Act
        $this->service->execute($command);

        // Assert - Event dispatched with correct data
        $this->assertEquals(1, $this->eventSpy->handleCallCount);
        $this->assertInstanceOf(AccountCreated::class, $this->eventSpy->domainEvent);
        
        /** @var AccountCreated $event */
        $event = $this->eventSpy->domainEvent;
        $this->assertEquals('acc-service-test', $event->accountId);
        $this->assertEquals('service@example.com', $event->emailOwner);
        $this->assertEquals('Service Test Account', $event->accountName);
    }
}
```

---

## Listener - Email Sending

### 1. Email Sending Listener

```php
<?php

namespace App\Infrastructure\Email;

use App\Domain\Account\Event\AccountCreated;
use Phariscope\Event\Psr14\Event;
use Phariscope\Event\Psr14\ListenerInterface;

final class SendWelcomeEmailListener implements ListenerInterface
{
    public function __construct(
        private readonly EmailServiceInterface $emailService
    ) {
    }

    public function handle(Event $event): bool
    {
        // 🎯 CHECK EVENT TYPE
        if (!$event instanceof AccountCreated) {
            return false;
        }

        try {
            // 📧 SEND EMAIL IN REACTION TO THE EVENT
            $this->emailService->sendWelcomeEmail(
                $event->emailOwner,
                $event->accountName,
                $event->accountId
            );
            return true;
        } catch (\Throwable $e) {
            // Log error but don't fail the process
            error_log("Failed to send welcome email: " . $e->getMessage());
            return false;
        }
    }

    public function isSubscribedTo(Event $event): bool
    {
        return $event instanceof AccountCreated;
    }
}
```

### 2. Interface Email Service

```php
<?php

namespace App\Infrastructure\Email;

interface EmailServiceInterface
{
    public function sendWelcomeEmail(
        string $recipientEmail,
        string $accountName,
        string $accountId
    ): void;
}
```

### 3. Listener Testing - Focus on Events

```php
<?php

namespace App\Tests\Infrastructure\Email;

use App\Domain\Account\Event\AccountCreated;
use App\Infrastructure\Email\SendWelcomeEmailListener;
use App\Infrastructure\Email\EmailServiceInterface;
use Phariscope\Event\Psr14\Event;
use PHPUnit\Framework\TestCase;

final class SendWelcomeEmailListenerEventTest extends TestCase
{
    private EmailServiceInterface $mockEmailService;
    private SendWelcomeEmailListener $listener;

    protected function setUp(): void
    {
        $this->mockEmailService = $this->createMock(EmailServiceInterface::class);
        $this->listener = new SendWelcomeEmailListener($this->mockEmailService);
    }

    public function testListenerHandlesAccountCreatedEvent(): void
    {
        // Arrange
        $event = new AccountCreated(
            'acc-789',
            'welcome@example.com',
            'Welcome Account'
        );

        $this->mockEmailService
            ->expects($this->once())
            ->method('sendWelcomeEmail')
            ->with(
                'welcome@example.com',
                'Welcome Account',
                'acc-789'
            );

        // Act - 🎯 EVENT HANDLING
        $result = $this->listener->handle($event);

        // Assert
        $this->assertTrue($result);
    }

    public function testListenerSubscriptionToAccountCreatedEvent(): void
    {
        // Arrange
        $event = new AccountCreated('acc-123', 'test@example.com', 'Test');

        // Act & Assert - 🎯 SUBSCRIPTION VERIFICATION
        $this->assertTrue($this->listener->isSubscribedTo($event));
    }

    public function testListenerIgnoresOtherEvents(): void
    {
        // Arrange
        $otherEvent = new class extends Event {};

        // Act & Assert - Should not be subscribed to other events
        $this->assertFalse($this->listener->isSubscribedTo($otherEvent));
        $this->assertFalse($this->listener->handle($otherEvent));
    }
}
```

---

## Infrastructure Layer - Controller

### 1. Controller with Event Management

```php
<?php

namespace App\Infrastructure\Controller;

use App\Application\Account\CreateAccountCommand;
use App\Application\Account\CreateAccountService;
use Phariscope\Event\EventDispatcher;

final class AccountController
{
    public function __construct(
        private readonly CreateAccountService $createAccountService,
        private readonly EventDispatcher $eventDispatcher
    ) {
    }

    /**
     * POST /api/accounts
     */
    public function createAccount(array $requestData): array
    {
        try {
            // Create command
            $command = new CreateAccountCommand(
                $requestData['id'],
                $requestData['email'],
                $requestData['name']
            );

            // 🎯 EXECUTE SERVICE (which dispatches the event)
            $account = $this->createAccountService->execute($command);

            // 🎯 DISTRIBUTE EVENTS (if not in immediate mode)
            if (!$this->eventDispatcher->isImmediateDistributionEnabled()) {
                $this->eventDispatcher->distribute();
            }

            return [
                'success' => true,
                'data' => [
                    'id' => $account->id(),
                    'email' => $account->emailOwner(),
                    'name' => $account->name()
                ]
            ];

        } catch (\Throwable $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}
```

### 2. Controller Testing - Focus on Events

```php
<?php

namespace App\Tests\Infrastructure\Controller;

use App\Application\Account\CreateAccountService;
use App\Domain\Account\Event\AccountCreated;
use App\Infrastructure\Account\AccountRepositoryInMemory;
use App\Infrastructure\Controller\AccountController;
use App\Infrastructure\Email\EmailServiceInterface;
use App\Infrastructure\Email\SendWelcomeEmailListener;
use Phariscope\Event\EventDispatcher;
use Phariscope\Event\Tools\SpyListener;
use PHPUnit\Framework\TestCase;

final class AccountControllerEventTest extends TestCase
{
    private AccountRepositoryInMemory $repository;
    private EmailServiceInterface $mockEmailService;
    private EventDispatcher $dispatcher;
    private SpyListener $eventSpy;
    private AccountController $controller;

    protected function setUp(): void
    {
        // Reset dispatcher
        EventDispatcher::tearDown();
        $this->dispatcher = EventDispatcher::instance();
        
        // In-memory repository (no mock)
        $this->repository = new AccountRepositoryInMemory();
        $this->mockEmailService = $this->createMock(EmailServiceInterface::class);
        
        // 🕵️ SPY TO VERIFY EVENTS
        $this->eventSpy = new SpyListener();
        $this->dispatcher->subscribe($this->eventSpy);
        
        // Real email listener
        $emailListener = new SendWelcomeEmailListener($this->mockEmailService);
        $this->dispatcher->subscribe($emailListener);
        
        // Service and controller
        $service = new CreateAccountService($this->repository, $this->dispatcher);
        $this->controller = new AccountController($service, $this->dispatcher);
    }

    public function testControllerDispatchesEventThroughCompleteFlow(): void
    {
        // Arrange
        $requestData = [
            'id' => 'acc-controller-test',
            'email' => 'controller@example.com',
            'name' => 'Controller Test Account'
        ];

        $this->mockEmailService
            ->expects($this->once())
            ->method('sendWelcomeEmail');

        // Act
        $response = $this->controller->createAccount($requestData);

        // Assert - 🎯 THE EVENT WAS DISPATCHED
        $this->assertTrue($response['success']);
        $this->assertEquals(1, $this->eventSpy->handleCallCount);
        $this->assertInstanceOf(AccountCreated::class, $this->eventSpy->domainEvent);
        
        /** @var AccountCreated $event */
        $event = $this->eventSpy->domainEvent;
        $this->assertEquals('acc-controller-test', $event->accountId);
        $this->assertEquals('controller@example.com', $event->emailOwner);
    }
}
```

---

## Event Integration Testing

### Complete Event Flow Testing

```php
<?php

namespace App\Tests\Integration;

use App\Application\Account\CreateAccountCommand;
use App\Application\Account\CreateAccountService;
use App\Domain\Account\Event\AccountCreated;
use App\Infrastructure\Account\AccountRepositoryInMemory;
use App\Infrastructure\Email\EmailServiceInterface;
use App\Infrastructure\Email\SendWelcomeEmailListener;
use Phariscope\Event\EventDispatcher;
use Phariscope\Event\Tools\SpyListener;
use PHPUnit\Framework\TestCase;

final class AccountCreationEventFlowTest extends TestCase
{
    private EventDispatcher $dispatcher;
    private SpyListener $eventSpy;

    protected function setUp(): void
    {
        // Complete reset for integration
        EventDispatcher::tearDown();
        $this->dispatcher = EventDispatcher::instance();
        
        // 🕵️ SPY TO TRACE ALL EVENTS
        $this->eventSpy = new SpyListener();
        $this->dispatcher->subscribe($this->eventSpy);
    }

    public function testCompleteEventFlowFromDomainToInfrastructure(): void
    {
        // Arrange - Complete configuration
        $repository = new AccountRepositoryInMemory();
        $mockEmailService = $this->createMock(EmailServiceInterface::class);
        
        // 🎯 REGISTER THE LISTENER
        $emailListener = new SendWelcomeEmailListener($mockEmailService);
        $this->dispatcher->subscribe($emailListener);
        
        $service = new CreateAccountService($repository, $this->dispatcher);

        $mockEmailService
            ->expects($this->once())
            ->method('sendWelcomeEmail')
            ->with(
                'integration@example.com',
                'Integration Test Account',
                'acc-integration'
            );

        // Act - 🎯 COMPLETE FLOW WITH EVENTS
        $command = new CreateAccountCommand(
            'acc-integration',
            'integration@example.com',
            'Integration Test Account'
        );

        $account = $service->execute($command);

        // Assert - 🎯 EVENT FLOW VERIFICATION
        // 1. The aggregate was created
        $this->assertEquals('acc-integration', $account->id());
        
        // 2. The event was captured by the spy
        $this->assertEquals(1, $this->eventSpy->handleCallCount);
        $this->assertInstanceOf(AccountCreated::class, $this->eventSpy->domainEvent);
        
        // 3. The event contains the correct data
        /** @var AccountCreated $capturedEvent */
        $capturedEvent = $this->eventSpy->domainEvent;
        $this->assertEquals('integration@example.com', $capturedEvent->emailOwner);
        $this->assertEquals('Integration Test Account', $capturedEvent->accountName);
        
        // 4. The email listener was called (verified by mock)
    }
}
```

---

## Configuration and Usage

### Application Bootstrap with Events

```php
<?php

// bootstrap.php - Application configuration

use App\Infrastructure\Email\SendWelcomeEmailListener;
use App\Infrastructure\Email\SmtpEmailService;
use Phariscope\Event\EventDispatcher;

// 🎯 DISPATCHER CONFIGURATION
$dispatcher = EventDispatcher::instance();

// Option 1: Immediate distribution (events processed on dispatch)
$dispatcher->distributeImmediately();

// Option 2: Manual distribution (events queued)
// $dispatcher->disableImmediateDistribution();
// Then call $dispatcher->distribute() when needed

// 🎯 REGISTER LISTENERS
$emailService = new SmtpEmailService(/* SMTP config */);
$welcomeEmailListener = new SendWelcomeEmailListener($emailService);
$dispatcher->subscribe($welcomeEmailListener);

// 📝 OPTIONAL: Logger configuration for errors
$logger = new \Monolog\Logger('events');
$dispatcher->setLogger($logger);
```

### Complete Usage Example

```php
<?php

use App\Application\Account\CreateAccountCommand;
use App\Application\Account\CreateAccountService;
use App\Infrastructure\Account\AccountRepositoryInMemory;
use Phariscope\Event\EventDispatcher;

// Configuration (see bootstrap.php above)
$dispatcher = EventDispatcher::instance();
$repository = new AccountRepositoryInMemory();
$service = new CreateAccountService($repository, $dispatcher);

// 🎯 USAGE: Account creation
$command = new CreateAccountCommand(
    'acc-example',
    'user@example.com',
    'Example Account'
);

// Service execution will:
// 1. Create the Account aggregate
// 2. Dispatch the AccountCreated event  
// 3. Persist the account
// 4. Distribute events (welcome email sent automatically)
$account = $service->execute($command);

echo "Account created: {$account->id()}\n";
// Welcome email was sent automatically via the event!
```

---

## Key Points for Events

### 🎯 **Event Dispatching**
1. **In Aggregate** : `EventDispatcher::instance()->dispatch($event)`
2. **Distribution** : `$dispatcher->distribute()` or immediate mode
3. **Testing** : Use `SpyListener` to capture events

### 🕵️ **Event Testing**
1. **SpyListener** : Captures all events for verification
2. **Type checking** : `assertInstanceOf(AccountCreated::class, $event)`
3. **Data verification** : Access to event properties

### 📧 **Listeners**
1. **Subscription** : `isSubscribedTo(Event $event): bool`
2. **Handling** : `handle(Event $event): bool`
3. **Decoupling** : Listeners only know about the event

### 🔄 **Distribution Modes**
1. **Immediate** : `distributeImmediately()` - events processed instantly
2. **Queued** : `disableImmediateDistribution()` + manual `distribute()`
3. **Testing** : Always use manual mode for better control

This documentation focuses on the essentials: **how to use events** in a DDD architecture with appropriate testing.