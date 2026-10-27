<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia;

uses(Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    Storage::fake('public');
});

test('profile picture can be uploaded', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->post('/account/avatar', [
            'avatar' => UploadedFile::fake()->image('avatar.jpg', 200, 200),
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertInertiaFlash('toasts', [['type' => 'success', 'message' => 'Profile picture updated successfully.']])
        ->assertRedirect('/account/profile');

    $path = $user->refresh()->avatar_path;

    expect($path)->toStartWith('avatars/');
    Storage::disk('public')->assertExists($path);

    $this->get('/account/profile')->assertInertia(
        fn (AssertableInertia $page) => $page->where('auth.user.avatar', Storage::disk('public')->url($path)),
    );
});

test('uploading a new profile picture removes the previous one', function () {
    $previousPath = UploadedFile::fake()->image('first.jpg')->store('avatars', 'public');
    $user = User::factory()->create(['avatar_path' => $previousPath]);

    $this->actingAs($user)->post('/account/avatar', [
        'avatar' => UploadedFile::fake()->image('second.png'),
    ]);

    Storage::disk('public')->assertMissing($previousPath);
    Storage::disk('public')->assertExists($user->refresh()->avatar_path);
});

test('profile picture must be an image of at most 2 MB', function (UploadedFile $file) {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->from('/account/profile')
        ->post('/account/avatar', ['avatar' => $file]);

    $response
        ->assertSessionHasErrors('avatar')
        ->assertRedirect('/account/profile');

    expect($user->refresh()->avatar_path)->toBeNull();
})->with([
    'not an image' => fn () => UploadedFile::fake()->create('avatar.pdf', 100, 'application/pdf'),
    'too large' => fn () => UploadedFile::fake()->image('avatar.jpg')->size(2049),
]);

test('profile picture can be removed', function () {
    $path = UploadedFile::fake()->image('avatar.jpg')->store('avatars', 'public');
    $user = User::factory()->create(['avatar_path' => $path]);

    $response = $this
        ->actingAs($user)
        ->delete('/account/avatar');

    $response
        ->assertInertiaFlash('toasts', [['type' => 'success', 'message' => 'Profile picture removed.']])
        ->assertRedirect('/account/profile');

    expect($user->refresh()->avatar_path)->toBeNull();
    Storage::disk('public')->assertMissing($path);
});

test('profile picture is removed when the account is deleted', function () {
    $path = UploadedFile::fake()->image('avatar.jpg')->store('avatars', 'public');
    $user = User::factory()->create(['avatar_path' => $path]);

    $this->actingAs($user)->delete('/account', ['password' => 'password']);

    expect($user->fresh())->toBeNull();
    Storage::disk('public')->assertMissing($path);
});

test('profile picture cannot be changed when the updateProfile gate denies it', function () {
    Gate::define('updateProfile', fn (User $user): bool => false);
    $user = User::factory()->create(['avatar_path' => 'avatars/existing.jpg']);

    $this->actingAs($user)
        ->post('/account/avatar', ['avatar' => UploadedFile::fake()->image('avatar.jpg')])
        ->assertForbidden();

    $this->actingAs($user)
        ->delete('/account/avatar')
        ->assertForbidden();

    expect($user->refresh()->avatar_path)->toBe('avatars/existing.jpg');
});
