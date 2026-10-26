<?php

declare(strict_types=1);

use App\Actions\Account\ListBrowserSessionsAction;
use App\Models\User;
use Illuminate\Support\Facades\DB;

function insertSession(string $id, ?int $userId, string $userAgent = 'Mozilla/5.0 (Macintosh) Chrome/130.0 Safari/537.36'): void
{
    DB::table('sessions')->insert([
        'id' => $id,
        'user_id' => $userId,
        'ip_address' => '127.0.0.1',
        'user_agent' => $userAgent,
        'payload' => '',
        'last_activity' => now()->getTimestamp(),
    ]);
}

test('lists the user\'s database sessions and flags the current device', function () {
    config(['session.driver' => 'database']);
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    insertSession('current-session', $user->id);
    insertSession('other-device', $user->id);
    insertSession('someone-else', $otherUser->id);

    $sessions = app(ListBrowserSessionsAction::class)->handle($user, 'current-session');

    expect($sessions)->toHaveCount(2)
        ->and($sessions->where('isCurrentDevice', true))->toHaveCount(1)
        ->and($sessions->first()?->platform)->toBe('macOS')
        ->and($sessions->first()?->browser)->toBe('Chrome');
});

test('lists no sessions when the session driver is not database', function () {
    config(['session.driver' => 'array']);
    $user = User::factory()->create();
    insertSession('other-device', $user->id);

    expect(app(ListBrowserSessionsAction::class)->handle($user, 'current-session'))->toBeEmpty();
});

test('security page shares the browser sessions', function () {
    config(['session.driver' => 'database']);
    $user = User::factory()->create();
    insertSession('other-device', $user->id);

    $this->actingAs($user)
        ->withSession(['auth.password_confirmed_at' => time()])
        ->get(route('account.security'))
        ->assertInertia(fn ($page) => $page
            ->has('sessions', 1)
            ->where('sessions.0.isCurrentDevice', false)
            ->missing('sessions.0.id'));
});

test('other browser sessions can be logged out', function () {
    config(['session.driver' => 'database']);
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    insertSession('other-device', $user->id);
    insertSession('someone-else', $otherUser->id);

    $this->actingAs($user)
        ->from(route('account.security'))
        ->delete(route('account.security.sessions.destroy'), ['password' => 'password'])
        ->assertSessionHasNoErrors()
        ->assertInertiaFlash('toasts', [['type' => 'success', 'message' => 'Logged out of your other browser sessions.']]);

    expect(DB::table('sessions')->where('id', 'other-device')->exists())->toBeFalse()
        ->and(DB::table('sessions')->where('id', 'someone-else')->exists())->toBeTrue();
});

test('other browser sessions are kept when the password is wrong', function () {
    config(['session.driver' => 'database']);
    $user = User::factory()->create();
    insertSession('other-device', $user->id);

    $this->actingAs($user)
        ->from(route('account.security'))
        ->delete(route('account.security.sessions.destroy'), ['password' => 'wrong-password'])
        ->assertSessionHasErrors('password');

    expect(DB::table('sessions')->where('id', 'other-device')->exists())->toBeTrue();
});
