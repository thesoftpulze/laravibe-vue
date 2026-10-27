<?php

declare(strict_types=1);

use App\Models\User;
use Database\Seeders\DatabaseSeeder;

uses(Illuminate\Foundation\Testing\RefreshDatabase::class);

test('it seeds an administrator, a regular user and sample users', function () {
    $this->seed(DatabaseSeeder::class);

    expect(User::where('email', 'admin@example.com')->sole()->is_admin)->toBeTrue()
        ->and(User::where('email', 'test@example.com')->sole()->is_admin)->toBeFalse()
        ->and(User::count())->toBe(32);
});

test('it seeds nothing in production', function () {
    app()->detectEnvironment(fn (): string => 'production');

    $this->seed(DatabaseSeeder::class);

    expect(User::count())->toBe(0);
});
