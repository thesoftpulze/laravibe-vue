<?php

declare(strict_types=1);

if (! function_exists('authUser')) {
    /**
     * Get the authenticated user or throw an exception if not authenticated.
     */
    function authUser(): App\Models\User
    {
        $authUser = Illuminate\Support\Facades\Auth::user();
        throw_if(is_null($authUser), Illuminate\Auth\AuthenticationException::class);

        return $authUser;
    }
}

if (! function_exists('vue')) {
    /**
     * @param  array<string, mixed>  $props
     * @param  array<string, mixed>|Illuminate\Contracts\Support\Arrayable<string, mixed>  $metaProps
     */
    function vue(string $name, array $props = [], array|Illuminate\Contracts\Support\Arrayable $metaProps = []): Inertia\Response
    {
        if ($metaProps instanceof Illuminate\Contracts\Support\Arrayable) {
            $metaProps = $metaProps->toArray();
        }

        return inertia($name, [...$props, 'meta' => $metaProps]);
    }
}

if (! function_exists('optionalProp')) {
    /**
     * Include the prop only when a partial reload explicitly requests it.
     */
    function optionalProp(callable $callback): Inertia\OptionalProp
    {
        return Inertia\Inertia::optional($callback);
    }
}

if (! function_exists('deferProp')) {
    /**
     * Load the prop in a follow-up request after the page renders.
     */
    function deferProp(callable $callback, string $group = 'default'): Inertia\DeferProp
    {
        return Inertia\Inertia::defer($callback, $group);
    }
}

if (! function_exists('alwaysProp')) {
    /**
     * Include the prop on every response, even partial reloads that don't request it.
     */
    function alwaysProp(mixed $value): Inertia\AlwaysProp
    {
        return Inertia\Inertia::always($value);
    }
}
