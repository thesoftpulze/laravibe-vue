<?php

declare(strict_types=1);

use App\Models\User;
use App\Support\CarbonImmutable;

test('to array', function () {
    $user = User::factory()->create()->fresh();

    expect(array_keys($user->toArray()))->toEqualCanonicalizing([
        'id',
        'name', 'email',
        'email_verified_at',
        'is_admin',
        'created_at', 'updated_at',
    ]);
});

test('casts', function () {
    $user = User::factory()->create()->fresh();

    expect($user)
        ->id->toBeInt()
        ->name->toBeString()
        ->email->toBeString()
        ->email_verified_at->toBeInstanceOf(CarbonImmutable::class)
        ->is_admin->toBeBool()
        ->created_at->toBeInstanceOf(CarbonImmutable::class)
        ->updated_at->toBeInstanceOf(CarbonImmutable::class);
});
