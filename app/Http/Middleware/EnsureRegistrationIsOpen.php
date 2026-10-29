<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Settings\AuthenticationSettings;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final readonly class EnsureRegistrationIsOpen
{
    public function __construct(
        private AuthenticationSettings $settings,
    ) {}

    /**
     * Hide Fortify's registration routes while registration is closed.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        abort_if(! $this->settings->allowRegistration && $request->routeIs('register', 'register.store'), 404);

        return $next($request);
    }
}
