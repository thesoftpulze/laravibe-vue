<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Support\Facades\Gate;

uses(Illuminate\Foundation\Testing\RefreshDatabase::class);

test('administrators may change the role of and delete other users but not themselves', function (string $ability) {
    $admin = User::factory()->admin()->create();

    expect(Gate::forUser($admin)->allows($ability, User::factory()->create()))->toBeTrue()
        ->and(Gate::forUser($admin)->allows($ability, User::factory()->admin()->create()))->toBeTrue()
        ->and(Gate::forUser($admin)->allows($ability, $admin))->toBeFalse()
        ->and(Gate::forUser(User::factory()->create())->allows($ability, User::factory()->create()))->toBeFalse();
})->with(['updateUserRole', 'deleteUser']);

test('administrators may resend verification only to unverified users', function () {
    $admin = User::factory()->admin()->create();
    $unverified = User::factory()->unverified()->create();

    expect(Gate::forUser($admin)->allows('resendUserVerification', $unverified))->toBeTrue()
        ->and(Gate::forUser($admin)->allows('resendUserVerification', User::factory()->create()))->toBeFalse()
        ->and(Gate::forUser(User::factory()->create())->allows('resendUserVerification', $unverified))->toBeFalse();
});
