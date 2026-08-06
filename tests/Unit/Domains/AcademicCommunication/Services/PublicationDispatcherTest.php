<?php

declare(strict_types=1);

use App\Domains\AcademicCommunication\Application\DTOs\PublicationDeliveryOutcome;
use App\Domains\AcademicCommunication\Application\DTOs\PublicationPayload;
use App\Domains\AcademicCommunication\Application\Services\PublicationDispatcher;
use App\Domains\AcademicCommunication\Domain\Contracts\PublicationChannelContract;
use App\Domains\AcademicCommunication\Domain\Enums\DocumentChannel;
use App\Domains\AcademicCommunication\Domain\Exceptions\PublicationChannelUnavailableException;

/**
 * Channel drivers are resolved through the config-driven driver map. Channels
 * that are declared but not implemented yet (Email, SMS, Push) are refused with
 * the standard unavailable exception instead of silently doing nothing.
 */
final class FakeChannel implements PublicationChannelContract
{
    public bool $available = true;

    public function channel(): DocumentChannel
    {
        return DocumentChannel::InternalPortal;
    }

    public function isAvailable(): bool
    {
        return $this->available;
    }

    public function deliver(PublicationPayload $payload): PublicationDeliveryOutcome
    {
        return PublicationDeliveryOutcome::success();
    }
}

function makeDispatcher(array $drivers): PublicationDispatcher
{
    return new PublicationDispatcher($drivers, app());
}

test('the dispatcher resolves an implemented channel driver', function (): void {
    $dispatcher = makeDispatcher([DocumentChannel::InternalPortal->value => FakeChannel::class]);

    expect($dispatcher->driver(DocumentChannel::InternalPortal))->toBeInstanceOf(FakeChannel::class);
});

test('the dispatcher returns null for channels without a driver', function (): void {
    $dispatcher = makeDispatcher([]);

    expect($dispatcher->driver(DocumentChannel::ParentPortal))->toBeNull();
});

test('the dispatcher returns null for not-implemented channels', function (): void {
    $dispatcher = makeDispatcher([DocumentChannel::Email->value => FakeChannel::class]);

    expect($dispatcher->driver(DocumentChannel::Email))->toBeNull();
});

test('requireDriver refuses a not-implemented channel', function (): void {
    $dispatcher = makeDispatcher([]);

    $dispatcher->requireDriver(DocumentChannel::Sms);
})->throws(PublicationChannelUnavailableException::class);

test('requireDriver refuses a missing driver with the channel name', function (): void {
    $dispatcher = makeDispatcher([]);

    $dispatcher->requireDriver(DocumentChannel::TeacherPortal, 'school-1');
})->throws(PublicationChannelUnavailableException::class, 'teacher_portal');

test('requireDriver refuses an unavailable driver', function (): void {
    $channel = new FakeChannel();
    $channel->available = false;

    $this->app->instance(FakeChannel::class, $channel);

    $dispatcher = makeDispatcher([DocumentChannel::InternalPortal->value => FakeChannel::class]);

    $dispatcher->requireDriver(DocumentChannel::InternalPortal, 'school-1');
})->throws(PublicationChannelUnavailableException::class);

test('requireDriver returns the driver when implemented and available', function (): void {
    $this->app->instance(FakeChannel::class, new FakeChannel());

    $dispatcher = makeDispatcher([DocumentChannel::InternalPortal->value => FakeChannel::class]);

    expect($dispatcher->requireDriver(DocumentChannel::InternalPortal, 'school-1'))
        ->toBeInstanceOf(FakeChannel::class);
});
