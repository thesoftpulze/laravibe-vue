<?php

declare(strict_types=1);

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\DTOs\PageMeta;
use App\Http\Responses\EmailVerificationNotificationSentResponse;
use App\Http\Responses\PasskeyDeletedResponse;
use App\Http\Responses\PasskeyRegistrationResponse;
use App\Http\Responses\PasswordResetLinkRequestResponse;
use App\Http\Responses\TwoFactorConfirmedResponse;
use App\Http\Responses\TwoFactorDisabledResponse;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use Inertia\Response;
use Laravel\Fortify\Contracts\EmailVerificationNotificationSentResponse as EmailVerificationNotificationSentResponseContract;
use Laravel\Fortify\Contracts\FailedPasswordResetLinkRequestResponse;
use Laravel\Fortify\Contracts\SuccessfulPasswordResetLinkRequestResponse;
use Laravel\Fortify\Contracts\TwoFactorConfirmedResponse as TwoFactorConfirmedResponseContract;
use Laravel\Fortify\Contracts\TwoFactorDisabledResponse as TwoFactorDisabledResponseContract;
use Laravel\Fortify\Features;
use Laravel\Fortify\Fortify;
use Laravel\Passkeys\Contracts\PasskeyDeletedResponse as PasskeyDeletedResponseContract;
use Laravel\Passkeys\Contracts\PasskeyRegistrationResponse as PasskeyRegistrationResponseContract;
use Override;

final class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    #[Override]
    public function register(): void
    {
        $this->app->singleton(EmailVerificationNotificationSentResponseContract::class, EmailVerificationNotificationSentResponse::class);
        $this->app->singleton(SuccessfulPasswordResetLinkRequestResponse::class, PasswordResetLinkRequestResponse::class);
        $this->app->singleton(FailedPasswordResetLinkRequestResponse::class, PasswordResetLinkRequestResponse::class);
        $this->app->singleton(TwoFactorConfirmedResponseContract::class, TwoFactorConfirmedResponse::class);
        $this->app->singleton(TwoFactorDisabledResponseContract::class, TwoFactorDisabledResponse::class);
        $this->app->singleton(PasskeyRegistrationResponseContract::class, PasskeyRegistrationResponse::class);
        $this->app->singleton(PasskeyDeletedResponseContract::class, PasskeyDeletedResponse::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureActions();
        $this->configureViews();
        $this->configureRateLimiting();
    }

    /**
     * Configure Fortify actions.
     */
    private function configureActions(): void
    {
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);
        Fortify::createUsersUsing(CreateNewUser::class);
    }

    /**
     * Configure Fortify views.
     */
    private function configureViews(): void
    {
        Fortify::loginView(fn (Request $request): Response => vue('auth/Login', [
            'canResetPassword' => Features::enabled(Features::resetPasswords()),
            'status' => $request->session()->get('status'),
        ], new PageMeta(
            heading: 'Log in to your account',
            subheading: 'Enter your email and password below to log in',
            title: 'Log in',
        )));

        Fortify::registerView(fn (): Response => vue('auth/Register', [
            'passwordRules' => Password::defaults()->toPasswordRulesString(),
        ], new PageMeta(
            heading: 'Create an account',
            subheading: 'Enter your details below to create your account',
            title: 'Register',
        )));

        Fortify::requestPasswordResetLinkView(fn (Request $request): Response => vue('auth/ForgotPassword', [
            'status' => $request->session()->get('status'),
        ], new PageMeta(
            heading: 'Forgot password',
            subheading: 'Enter your email to receive a password reset link',
            title: 'Forgot password',
        )));

        Fortify::resetPasswordView(fn (Request $request): Response => vue('auth/ResetPassword', [
            'email' => $request->string('email')->value(),
            'token' => $request->route('token'),
            'passwordRules' => Password::defaults()->toPasswordRulesString(),
        ], new PageMeta(
            heading: 'Reset password',
            subheading: 'Please enter your new password below',
            title: 'Reset password',
        )));

        Fortify::verifyEmailView(fn (Request $request): Response => vue('auth/VerifyEmail', [
            'status' => $request->session()->get('status'),
        ], new PageMeta(
            heading: 'Verify email',
            subheading: 'Please verify your email address by clicking on the link we just emailed to you.',
            title: 'Email verification',
        )));

        Fortify::confirmPasswordView(fn (): Response => vue('auth/ConfirmPassword', [
            'hasPasskeys' => authUser()->hasPasskeysEnabled(),
        ], new PageMeta(
            heading: 'Confirm your password',
            subheading: 'This is a secure area of the application. Please confirm your password before continuing.',
            title: 'Confirm password',
        )));

        Fortify::twoFactorChallengeView(fn (): Response => vue('auth/TwoFactorChallenge', metaProps: new PageMeta(
            heading: 'Two-factor authentication',
            subheading: 'Enter the code from your authenticator app, or one of your recovery codes.',
            title: 'Two-factor authentication',
        )));
    }

    /**
     * Throttle two-factor challenge attempts per pending login, and passkey ceremonies per credential or session and IP.
     */
    private function configureRateLimiting(): void
    {
        RateLimiter::for('two-factor', fn (Request $request): Limit => Limit::perMinute(5)->by($request->session()->get('login.id')));

        RateLimiter::for('passkeys', fn (Request $request): Limit => Limit::perMinute(10)->by(
            ($request->string('credential.id')->value() ?: $request->session()->getId()) . '|' . $request->ip(),
        ));
    }
}
