<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

uses(Illuminate\Foundation\Testing\RefreshDatabase::class);

test('administrators can delete another user with their profile picture and sessions', function () {
    Storage::fake('public');
    config(['session.driver' => 'database']);

    $admin = User::factory()->admin()->create();
    $avatar = UploadedFile::fake()->image('avatar.jpg')->store('avatars', 'public');
    $user = User::factory()->create(['name' => 'Jane Doe', 'avatar_path' => $avatar]);
    DB::table('sessions')->insert([
        ['id' => 'deleted-user-session', 'user_id' => $user->id, 'payload' => '', 'last_activity' => now()->timestamp],
        ['id' => 'admin-session', 'user_id' => $admin->id, 'payload' => '', 'last_activity' => now()->timestamp],
    ]);

    $this->actingAs($admin)
        ->from(route('administration.users.index'))
        ->delete(route('administration.users.destroy', $user))
        ->assertRedirect(route('administration.users.index'))
        ->assertInertiaFlash('toasts', [['type' => 'success', 'message' => 'Jane Doe was deleted.']]);

    $this->assertModelMissing($user);
    Storage::disk('public')->assertMissing($avatar);
    $this->assertDatabaseMissing('sessions', ['id' => 'deleted-user-session']);
    $this->assertDatabaseHas('sessions', ['id' => 'admin-session']);
});

test('administrators cannot delete themselves', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->delete(route('administration.users.destroy', $admin))
        ->assertForbidden();

    $this->assertModelExists($admin);
});

test('non-administrators cannot delete users', function () {
    $user = User::factory()->create();

    $this->actingAs(User::factory()->create())
        ->delete(route('administration.users.destroy', $user))
        ->assertForbidden();

    $this->assertModelExists($user);
});
