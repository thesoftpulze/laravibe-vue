<?php

declare(strict_types=1);

use App\Enums\ToastActionType;
use App\Enums\ToastType;
use App\Toast\DTOs\ToastActionPayload;
use App\Toast\DTOs\ToastPayload;
use App\Toast\Exceptions\ToastException;
use Illuminate\Http\Request;

it('can creates instance of ToastActionPayload and convert to array and json', function () {
    $copyAction = ToastActionPayload::copy('Text To Copy');

    expect($copyAction->toArray())
        ->toBe([
            'type' => 'copy',
            'payload' => 'Text To Copy',
            'label' => null,
        ])
        ->and($copyAction->toJson(JSON_UNESCAPED_SLASHES))->toBeJson()
        ->and(json_decode($copyAction->toJson(JSON_UNESCAPED_SLASHES), true))->toBe([
            'type' => 'copy',
            'payload' => 'Text To Copy',
            'label' => null,
        ]);
});

it('can creates instance of ToastPayload and convert to array and json', function () {
    $toast = new ToastPayload(ToastType::SUCCESS, 'Saved');

    expect($toast->toArray())
        ->toBe([
            'type' => 'success',
            'message' => 'Saved',
        ])
        ->and($toast->toJson(JSON_UNESCAPED_SLASHES))->toBeJson()
        ->and(json_decode($toast->toJson(JSON_UNESCAPED_SLASHES), true))->toBe([
            'type' => 'success',
            'message' => 'Saved',
        ]);
});

it('can create instances of ToastActionPayload using factory methods', function (): void {
    $copyAction = ToastActionPayload::copy('Text To Copy');
    $redirectAction = ToastActionPayload::redirect('https://example.com', 'Example Label');

    expect($copyAction)
        ->type->toBe(ToastActionType::COPY)
        ->payload->toBe('Text To Copy')
        ->label->toBeNull()
        ->and($redirectAction)
        ->type->toBe(ToastActionType::REDIRECT)
        ->payload->toBe('https://example.com')
        ->label->toBe('Example Label');
});

it('can create instances of ToastPayload using factory methods', function () {
    ToastPayload::success('Saved');
    ToastPayload::error('Saved');
    ToastPayload::info('Saved');
    ToastPayload::warning('Saved');

    expect(toast()->pull())->toBeArray()->toHaveCount(4);
});

it('can hydrate a toast payload from array with nested actions', function () {
    $payload = ToastPayload::fromArray([
        'type' => 'success',
        'message' => 'Saved',
        'actions' => [
            ['type' => 'copy', 'payload' => 'ref-1', 'label' => 'Copy'],
            ['type' => 'redirect', 'payload' => 'https://example.com', 'label' => 'Open'],
        ],
    ]);

    expect($payload)
        ->toBeInstanceOf(ToastPayload::class)
        ->type->toBe(ToastType::SUCCESS)
        ->message->toBe('Saved')
        ->actions->toBeArray()->toHaveCount(2)
        ->and($payload->actions[0])->toBeInstanceOf(ToastActionPayload::class)
        ->and($payload->actions[1])->toBeInstanceOf(ToastActionPayload::class)
        ->and($payload->toArray())
        ->toBe([
            'type' => 'success',
            'message' => 'Saved',
            'actions' => [
                ['type' => 'copy', 'payload' => 'ref-1', 'label' => 'Copy'],
                ['type' => 'redirect', 'payload' => 'https://example.com', 'label' => 'Open'],
            ],
        ]);
});

it('can hydrate toast payload metadata fields', function () {
    $payload = ToastPayload::fromArray([
        'type' => 'info',
        'message' => 'FYI',
        'duration' => '7500',
        'dismissible' => 'false',
    ]);

    expect($payload)
        ->type->toBe(ToastType::INFO)
        ->message->toBe('FYI')
        ->duration->toBe(7500)
        ->dismissible->toBeFalse()
        ->and($payload->toArray())
        ->toBe([
            'type' => 'info',
            'message' => 'FYI',
            'duration' => 7500,
            'dismissible' => false,
        ]);
});

it('throws when unknown keys are present in toast payload hydration', function () {
    ToastPayload::fromArray([
        'type' => 'success',
        'message' => 'Saved',
        'extra' => 'unexpected',
    ]);
})->throws(InvalidArgumentException::class, 'Unknown properties for App\\Toast\\DTOs\\ToastPayload: extra.');

it('throws when unknown keys are present in nested action hydration', function () {
    ToastPayload::fromArray([
        'type' => 'success',
        'message' => 'Saved',
        'actions' => [
            ['type' => 'copy', 'payload' => 'abc', 'label' => 'Copy', 'extra' => 'unexpected'],
        ],
    ]);
})->throws(InvalidArgumentException::class, 'Unknown properties for App\\Toast\\DTOs\\ToastActionPayload: extra.');

it('keeps toast queue capped and skips consecutive duplicates', function () {
    toast()->append(new ToastPayload(ToastType::SUCCESS, 'Duplicate'));
    toast()->append(new ToastPayload(ToastType::SUCCESS, 'Duplicate'));

    foreach (range(1, 6) as $index) {
        toast()->append(new ToastPayload(ToastType::INFO, 'Message ' . $index));
    }

    $toasts = toast()->pull();

    expect($toasts)
        ->toHaveCount(5)
        ->and($toasts[0])->toHaveKey('message', 'Message 2')
        ->and($toasts[4])->toHaveKey('message', 'Message 6');
});

it('can add toast to session when throw ToastException', function () {
    $message = 'Test error message';
    $exception = new ToastException($message);

    $request = Request::create('/test');

    $session = app('session');
    $session->start();

    $request->setLaravelSession($session->driver());

    $response = $exception->render($request);

    $toasts = toast()->pull();
    expect($toasts)
        ->toBeArray()
        ->toHaveCount(1)
        ->and($toasts[0])
        ->message->toBe($message)
        ->type->toBe('error')
        ->and($response)
        ->toBeInstanceOf(Illuminate\Http\RedirectResponse::class);
});

it('can add toast to session with custom type when throw ToastException', function () {
    $exception = new ToastException('Heads up', ToastType::WARNING);
    $request = Request::create('/test');

    $session = app('session');
    $session->start();
    $request->setLaravelSession($session->driver());

    $exception->render($request);

    $toasts = toast()->pull();

    expect($toasts)
        ->toHaveCount(1)
        ->and($toasts[0])
        ->toHaveKey('type', 'warning')
        ->toHaveKey('message', 'Heads up');
});

it('preserves positional exception codes and previous exceptions', function () {
    $previous = new RuntimeException('Original failure');
    $exception = new ToastException('Failed', ToastType::WARNING, 42, $previous);

    expect($exception->getMessage())->toBe('Failed')
        ->and($exception->getCode())->toBe(42)
        ->and($exception->getPrevious())->toBe($previous);
});

it('delivers queued toasts as inertia flash data on the next page', function () {
    $user = App\Models\User::factory()->create();

    $this->actingAs($user)
        ->followingRedirects()
        ->patch(route('account.update'), ['name' => 'Updated Name', 'email' => $user->email])
        ->assertInertia(fn (Inertia\Testing\AssertableInertia $page) => $page
            ->hasFlash('toasts', [['type' => 'success', 'message' => 'Profile updated successfully.']])
            ->missing('toasts'));
});

it('can hydrate a description and serializes it after the message', function () {
    $payload = ToastPayload::fromArray(['type' => 'success', 'message' => 'Saved', 'description' => 'Your changes are live.']);

    expect($payload->toArray())->toBe([
        'type' => 'success',
        'message' => 'Saved',
        'description' => 'Your changes are live.',
    ]);
});

it('throws when the toast duration is negative', function () {
    ToastPayload::fromArray(['type' => 'info', 'message' => 'FYI', 'duration' => -1]);
})->throws(InvalidArgumentException::class, 'Expected a non-negative number of milliseconds.');

it('creates a visit action for same-tab inertia visits', function () {
    expect(ToastActionPayload::visit('/account', 'View'))
        ->type->toBe(ToastActionType::VISIT)
        ->payload->toBe('/account')
        ->label->toBe('View');
});

it('queues a toast built with the fluent builder once it goes out of scope', function () {
    toast()->success('Saved')
        ->description('Your changes are live.')
        ->action(toastActionVisit('/account', 'View'))
        ->persistent()
        ->dismissible(false);

    expect(toast()->pull())->toBe([[
        'type' => 'success',
        'message' => 'Saved',
        'description' => 'Your changes are live.',
        'actions' => [['type' => 'visit', 'payload' => '/account', 'label' => 'View']],
        'duration' => 0,
        'dismissible' => false,
    ]]);
});

it('queues a builder toast only once when sent explicitly', function () {
    $pending = toast()->info('Heads up');
    $pending->send();
    unset($pending);

    expect(toast()->pull())->toHaveCount(1);
});

it('preserves non-negative builder durations', function (int $duration) {
    $pending = toast()->info('Heads up')->duration($duration);
    $pending->send();

    expect(toast()->pull()[0])->toHaveKey('duration', $duration);
})->with([0, 1, 7500]);

it('rejects negative builder durations without changing the previous duration', function () {
    $pending = toast()->info('Heads up')->duration(7500);

    expect(fn () => $pending->duration(-1))
        ->toThrow(InvalidArgumentException::class, 'Expected a non-negative number of milliseconds.');

    $pending->send();

    expect(toast()->pull()[0])->toHaveKey('duration', 7500);
});

it('adds the description and actions of a ToastException to the toast', function () {
    $request = Request::create('/test');
    $session = app('session');
    $session->start();
    $request->setLaravelSession($session->driver());

    (new ToastException('Payment failed', description: 'Your card was declined.', actions: [toastActionVisit('/billing', 'Update card')]))->render($request);

    expect(toast()->pull()[0])
        ->toHaveKey('description', 'Your card was declined.')
        ->toHaveKey('actions', [['type' => 'visit', 'payload' => '/billing', 'label' => 'Update card']]);
});
