<?php

declare(strict_types=1);

use App\Models\User;
use Database\Seeders\DatabaseSeeder;

uses(Illuminate\Foundation\Testing\RefreshDatabase::class);

test('it seeds an administrator and a regular user', function () {
    $this->seed(DatabaseSeeder::class);

    expect(User::where('email', 'admin@example.com')->sole()->is_admin)->toBeTrue()
        ->and(User::where('email', 'test@example.com')->sole()->is_admin)->toBeFalse();
});
