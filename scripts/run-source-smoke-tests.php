<?php

declare(strict_types=1);

if (is_file(__DIR__ . '/../vendor/autoload.php')) {
    require __DIR__ . '/../vendor/autoload.php';
} else {
    spl_autoload_register(static function (string $class): void {
        $prefix = 'DesignPatterns\\';
        if (! str_starts_with($class, $prefix)) {
            return;
        }
        $relative = str_replace('\\', '/', substr($class, strlen($prefix)));
        require __DIR__ . '/../src/' . $relative . '.php';
    });
}

use DesignPatterns\Application\Command\Command;
use DesignPatterns\Application\Command\CommandBus;
use DesignPatterns\Application\Command\CommandHandler;
use DesignPatterns\Behavioral\Observer\Event;
use DesignPatterns\Behavioral\Observer\EventDispatcher;
use DesignPatterns\Behavioral\Observer\EventListener;
use DesignPatterns\Behavioral\State\Order;
use DesignPatterns\Structural\Decorator\InMemoryMessageSender;
use DesignPatterns\Structural\Decorator\ValidatingMessageSender;
use DesignPatterns\Enterprise\ActiveRecord\NoteRecord;
use DesignPatterns\Enterprise\DataMapper\CustomerDataMapper;
use DesignPatterns\Enterprise\DataMapper\InMemoryCustomerRowGateway;
use DesignPatterns\Enterprise\ServiceLayer\CustomerRegistrationService;
use DesignPatterns\Enterprise\Migration\DualRunComparator;
use DesignPatterns\Enterprise\Messaging\DeduplicationWindow;
use DesignPatterns\Enterprise\Resilience\DistributedBulkhead\DistributedBulkhead;
use DesignPatterns\Enterprise\Resilience\DistributedBulkhead\InMemoryPermitStore;
use DesignPatterns\Enterprise\Testing\FailureInjector;
use DesignPatterns\Enterprise\Testing\InjectedFailure;
use DesignPatterns\Enterprise\Query\CustomerSearchCriteria;
use DesignPatterns\Enterprise\Query\SearchCustomers;
use DesignPatterns\Enterprise\Repository\Customer;
use DesignPatterns\Enterprise\Resilience\FailureKind;
use DesignPatterns\Enterprise\Resilience\CircuitBreaker;
use DesignPatterns\Enterprise\Resilience\CircuitOpenException;
use DesignPatterns\Enterprise\Resilience\CircuitState;
use DesignPatterns\Enterprise\Resilience\Bulkhead;
use DesignPatterns\Enterprise\Resilience\BulkheadRejectedException;
use DesignPatterns\Enterprise\Resilience\Backpressure\BoundedWorkQueue;
use DesignPatterns\Enterprise\Resilience\RetryPolicy;
use DesignPatterns\Enterprise\Resilience\RateLimiter\FixedWindowRateLimiter;
use DesignPatterns\Enterprise\Repository\InMemoryCustomerRepository;
use DesignPatterns\Enterprise\Specification\AndSpecification;
use DesignPatterns\Enterprise\Specification\Specification;
use DesignPatterns\Enterprise\UnitOfWork\InMemoryUnitOfWork;
use DesignPatterns\Domain\Money;
use DesignPatterns\Infrastructure\Idempotency\IdempotencyRecord;
use DesignPatterns\Infrastructure\Idempotency\InMemoryIdempotencyStore;
use DesignPatterns\Infrastructure\Outbox\InMemoryOutboxRepository;
use DesignPatterns\Infrastructure\Outbox\OutboxMessage;
use DesignPatterns\ReadModel\Page;

$serviceRepository = new InMemoryCustomerRepository();
$registration = new CustomerRegistrationService($serviceRepository);
assert($registration->register(9, 'service@example.com')->id === 9);

$mapper = new CustomerDataMapper(new InMemoryCustomerRowGateway());
$mapper->save(new Customer(8, 'mapper@example.com'));
assert($mapper->find(8)?->email === 'mapper@example.com');

NoteRecord::reset();
(new NoteRecord(1, 'smoke'))->save();
assert(NoteRecord::find(1)?->body === 'smoke');

$repository = new InMemoryCustomerRepository();
$repository->save(new Customer(1, 'active@example.com'));
$repository->save(new Customer(2, 'inactive@example.com', false));
assert(count($repository->activeCustomers()) === 1);
assert($repository->getById(1)->email === 'active@example.com');

$query = new SearchCustomers();
$result = $query->execute(
    [$repository->getById(1), $repository->getById(2)],
    new CustomerSearchCriteria(emailContains: 'active', active: true),
);
assert(count($result) === 1 && $result[0]->id === 1);

$active = new class implements Specification {
    public function isSatisfiedBy(object $candidate): bool
    {
        return $candidate instanceof Customer && $candidate->active;
    }
};
$email = new class implements Specification {
    public function isSatisfiedBy(object $candidate): bool
    {
        return $candidate instanceof Customer && str_ends_with($candidate->email, '@example.com');
    }
};
assert((new AndSpecification([$active, $email]))->isSatisfiedBy($repository->getById(1)));



$circuit = new CircuitBreaker(failureThreshold: 1, recoveryTimeoutSeconds: 2);
$circuitNow = new DateTimeImmutable('2026-01-01T00:00:00Z');
try {
    $circuit->execute(static fn (): never => throw new RuntimeException('dependency down'), $circuitNow);
} catch (RuntimeException) {
}
assert($circuit->state($circuitNow) === CircuitState::Open);
try {
    $circuit->execute(static fn (): string => 'blocked', $circuitNow);
    assert(false);
} catch (CircuitOpenException) {
}
assert($circuit->execute(static fn (): string => 'recovered', $circuitNow->modify('+2 seconds')) === 'recovered');

$bulkhead = new Bulkhead(1);
assert($bulkhead->execute(static fn (): string => 'bulkhead-ok') === 'bulkhead-ok');
try {
    $bulkhead->execute(static fn (): mixed => $bulkhead->execute(static fn (): string => 'nested'));
    assert(false);
} catch (BulkheadRejectedException) {
}
assert($bulkhead->active() === 0);



$boundedQueue = new BoundedWorkQueue(2);
assert($boundedQueue->enqueue('batch-1')->accepted);
assert($boundedQueue->enqueue('batch-2')->accepted);
assert(!$boundedQueue->enqueue('batch-3')->accepted);
assert($boundedQueue->dequeue() === 'batch-1');
assert($boundedQueue->enqueue('batch-3')->accepted);

$rateLimiter = new FixedWindowRateLimiter(limit: 2, windowSeconds: 60);
$rateNow = new DateTimeImmutable('2026-08-02T00:00:10Z');
assert($rateLimiter->acquire('tenant-a', $rateNow)->allowed);
assert($rateLimiter->acquire('tenant-a', $rateNow)->allowed);
assert(!$rateLimiter->acquire('tenant-a', $rateNow)->allowed);
assert($rateLimiter->acquire('tenant-a', $rateNow->modify('+60 seconds'))->allowed);

$retryDecision = (new RetryPolicy(maxAttempts: 3))->decide(FailureKind::Transient, 1, true);
assert($retryDecision->shouldRetry);
$ambiguousDecision = (new RetryPolicy())->decide(FailureKind::Ambiguous, 1, true);
assert($ambiguousDecision->requiresReconciliation);

$unitOfWork = new InMemoryUnitOfWork();
assert($unitOfWork->transactional(static fn (): string => 'committed') === 'committed');

$money = (new Money(1_000, 'USD'))->add(new Money(250, 'USD'));
assert($money->minor === 1_250);

$idempotency = new InMemoryIdempotencyStore();
$hash = hash('sha256', '{"amount":1000}');
$idempotency->save(new IdempotencyRecord('payment-1', $hash, 'accepted'));
assert($idempotency->find('payment-1')?->response === 'accepted');

$outbox = new InMemoryOutboxRepository();
$outbox->add(new OutboxMessage('evt-1', 'customer.created', ['id' => 1], new DateTimeImmutable('2026-01-01T00:00:00Z')));
assert(count($outbox->pending(10)) === 1);
$outbox->markPublished('evt-1');
assert($outbox->pending(10) === []);

$page = new Page([$repository->getById(1)], 'next-1');
assert($page->hasNextPage());



$commandBus = new CommandBus();
$commandBus->register(
    commandClass: SmokeCommand::class,
    handler: new class implements CommandHandler {
        public function handle(Command $command): mixed
        {
            return $command instanceof SmokeCommand ? strtoupper($command->value) : null;
        }
    },
);
assert($commandBus->dispatch(new SmokeCommand('ok')) === 'OK');

$receivedEvents = [];
$dispatcher = new EventDispatcher();
$dispatcher->subscribe('order.paid', new class($receivedEvents) implements EventListener {
    /** @param list<string> $receivedEvents */
    public function __construct(private array &$receivedEvents) {}
    public function handle(Event $event): void { $this->receivedEvents[] = $event->name(); }
});
$dispatcher->dispatch(new class implements Event { public function name(): string { return 'order.paid'; } });
assert($receivedEvents === ['order.paid']);

$order = new Order();
$order->pay();
$order->ship();
assert($order->state() === 'shipped');

$innerSender = new InMemoryMessageSender();
(new ValidatingMessageSender($innerSender))->send('room-1', 'Deployment completed');
assert(count($innerSender->sent()) === 1);


$distributedNow = new DateTimeImmutable('2026-08-02T00:00:00Z');
$distributedBulkhead = new DistributedBulkhead(new InMemoryPermitStore(), 1, 10);
$distributedLease = $distributedBulkhead->acquire('smoke-worker', $distributedNow);
assert($distributedBulkhead->release($distributedLease));

$failureInjector = new FailureInjector(42);
$injected = false;
for ($call = 1; $call <= 100; $call++) {
    try {
        $failureInjector->checkpoint('payment.after-provider', 5);
    } catch (InjectedFailure) {
        $injected = true;
        break;
    }
}
assert($injected);

$dualRun = new DualRunComparator(
    authoritative: static fn (int $value): array => ['value' => $value, 'timestamp' => 'old'],
    shadow: static fn (int $value): array => ['value' => $value, 'timestamp' => 'new'],
    normalizer: static fn (array $result): array => ['value' => $result['value']],
);
assert($dualRun->compare(10)->equivalent());


$deduplicationWindow = new DeduplicationWindow(30);
$dedupNow = new DateTimeImmutable('2026-08-02T00:00:00Z');
assert($deduplicationWindow->firstSeen('evt-smoke', $dedupNow));
assert(!$deduplicationWindow->firstSeen('evt-smoke', $dedupNow->modify('+5 seconds')));
assert($deduplicationWindow->firstSeen('evt-smoke', $dedupNow->modify('+31 seconds')));

echo "PASS: source smoke tests\n";

final readonly class SmokeCommand implements Command
{
    public function __construct(public string $value) {}
}
