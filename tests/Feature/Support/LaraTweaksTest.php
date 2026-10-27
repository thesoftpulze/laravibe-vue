<?php

declare(strict_types=1);

use App\Models\User;
use App\Support\LaraTweaks;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

afterEach(function (): void {
    URL::forceScheme(null);
    Password::defaults(fn (): ?Password => null);
    Model::shouldBeStrict();
    DB::prohibitDestructiveCommands(false);
});

it('keeps http urls when the app url uses http', function (): void {
    config(['app.url' => 'http://localhost']);
    URL::forceScheme(null);
    URL::setRequest(Request::create('http://localhost'));

    LaraTweaks::urls();

    expect(URL::to('/dashboard'))->toStartWith('http://');
});

it('forces https urls when the app url uses https', function (): void {
    config(['app.url' => 'https://localhost']);
    URL::forceScheme(null);
    URL::setRequest(Request::create('http://localhost'));

    LaraTweaks::urls();

    expect(URL::to('/dashboard'))->toStartWith('https://');
});

it('accepts simple passwords outside production', function (): void {
    app()->detectEnvironment(fn (): string => 'local');

    LaraTweaks::passwords();

    expect(Validator::make(['password' => 'password'], ['password' => Password::defaults()])->passes())->toBeTrue();
});

it('requires strong passwords in production', function (): void {
    app()->detectEnvironment(fn (): string => 'production');

    LaraTweaks::passwords();

    expect(Validator::make(['password' => 'password'], ['password' => Password::defaults()])->passes())->toBeFalse();
});

it('allows mass assignment of any attribute', function (): void {
    $user = User::make(['name' => 'Jane', 'email_verified_at' => now()]);

    expect($user->email_verified_at)->not->toBeNull();
});

it('enables strict models outside production', function (): void {
    app()->detectEnvironment(fn (): string => 'local');

    LaraTweaks::models();

    expect(Model::preventsLazyLoading())->toBeTrue();
});

it('disables strict models in production', function (): void {
    app()->detectEnvironment(fn (): string => 'production');

    LaraTweaks::models();

    expect(Model::preventsLazyLoading())->toBeFalse();
});

it('prohibits destructive database commands in production', function (): void {
    app()->detectEnvironment(fn (): string => 'production');

    LaraTweaks::dbCommands();

    $this->artisan('db:wipe', ['--force' => true])->assertFailed();
});

it('serializes json resources without a data wrapper', function (): void {
    LaraTweaks::resources();

    expect(JsonResource::$wrap)->toBeNull();
});
