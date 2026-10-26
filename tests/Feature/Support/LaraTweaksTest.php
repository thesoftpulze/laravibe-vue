<?php

declare(strict_types=1);

use App\Support\LaraTweaks;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

afterEach(function (): void {
    URL::forceScheme(null);
    Password::defaults(fn (): ?Password => null);
});

it('keeps http urls outside production', function (): void {
    app()->detectEnvironment(fn (): string => 'local');
    URL::forceScheme(null);
    URL::setRequest(Request::create('http://localhost'));

    LaraTweaks::urls();

    expect(URL::to('/dashboard'))->toStartWith('http://');
});

it('forces https urls in production', function (): void {
    app()->detectEnvironment(fn (): string => 'production');
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
