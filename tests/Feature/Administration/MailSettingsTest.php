<?php

declare(strict_types=1);

use App\Mail\TestEmail;
use App\Models\User;
use App\Settings\MailSettings;
use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\Mailer\Exception\TransportException;

test('administrators can view the mail settings with their env defaults', function () {
    config(['mail.default' => 'smtp', 'mail.from.name' => 'Laravibe', 'mail.from.address' => 'hello@example.com']);

    $this->actingAs(User::factory()->admin()->create())
        ->get(route('administration.settings.mail.edit'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('administration/settings/Mail')
            ->where('settings', ['fromName' => null, 'fromAddress' => null])
            ->where('defaults', ['fromName' => 'Laravibe', 'fromAddress' => 'hello@example.com'])
            ->where('mailer', 'smtp'));
});

test('administrators can change the sender', function () {
    $this->actingAs(User::factory()->admin()->create())
        ->put(route('administration.settings.mail.update'), ['fromName' => 'Acme', 'fromAddress' => 'team@acme.test'])
        ->assertSessionHasNoErrors()
        ->assertInertiaFlash('toasts', [['type' => 'success', 'message' => 'Settings saved.']]);

    expect(app(MailSettings::class)->toArray())->toBe(['fromName' => 'Acme', 'fromAddress' => 'team@acme.test'])
        ->and(config('mail.from'))->toBe(['address' => 'team@acme.test', 'name' => 'Acme']);
});

test('the sender address must be an email address', function () {
    $this->actingAs(User::factory()->admin()->create())
        ->put(route('administration.settings.mail.update'), ['fromAddress' => 'not-an-email'])
        ->assertSessionHasErrors('fromAddress');

    expect(app(MailSettings::class)->fromAddress)->toBeNull();
});

test('only administrators can manage the mail settings', function () {
    Mail::fake();
    $user = User::factory()->create();

    $this->actingAs($user)->get(route('administration.settings.mail.edit'))->assertForbidden();
    $this->actingAs($user)->put(route('administration.settings.mail.update'), ['fromName' => 'Acme'])->assertForbidden();
    $this->actingAs($user)->post(route('administration.settings.mail.test.store'))->assertForbidden();

    Mail::assertNothingSent();
});

test('administrators can send themselves a test email', function () {
    Mail::fake();
    $admin = User::factory()->admin()->create(['email' => 'admin@acme.test']);

    $this->actingAs($admin)
        ->post(route('administration.settings.mail.test.store'))
        ->assertInertiaFlash('toasts', [['type' => 'success', 'message' => 'Test email sent to admin@acme.test.']]);

    Mail::assertSent(TestEmail::class, fn (TestEmail $mail): bool => $mail->hasTo('admin@acme.test'));
});

test('a failed test email is reported to the administrator', function () {
    $this->mock(Mailer::class, fn ($mailer) => $mailer->shouldReceive('to->send')->andThrow(new TransportException('Connection refused.')));

    $this->actingAs(User::factory()->admin()->create())
        ->post(route('administration.settings.mail.test.store'))
        ->assertInertiaFlash('toasts', [[
            'type' => 'error',
            'message' => 'The test email could not be sent.',
            'description' => 'Connection refused.',
        ]]);
});

test('the test email names the application', function () {
    config(['app.name' => 'Acme']);

    new TestEmail()
        ->assertHasSubject('Test email from Acme')
        ->assertSeeInHtml('Your email is working');
});
