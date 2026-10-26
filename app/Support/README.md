# Support Layer

This folder contains framework-level support utilities shared across the backend.

## Files

- `CarbonImmutable.php` - application date class extending CarbonImmutable.
- `Helpers.php` - global helper functions used by controllers and Inertia responses.
- `LaraTweaks.php` - centralized app boot tweaks and framework defaults.

## LaraTweaks

`App\\Support\\LaraTweaks::default()` is called from `AppServiceProvider::boot()` and applies these defaults:

- Prohibit destructive DB commands in production.
- Force the HTTPS URL scheme in production.
- Require strong, uncompromised passwords in production via `Password::defaults()`.
- Set `App\\Support\\CarbonImmutable` as the default Date implementation.
- Enable strict Eloquent behavior.
- Disable default wrapping for JSON resources.
- Enable Vite prefetching with concurrency 3.

## CarbonImmutable

`App\\Support\\CarbonImmutable` is the app default date class.

API-safe formatters:

- `toApiDate()` => `Y-m-d`
- `toApiTime()` => `H:i:s`
- `toApiDatetime()` => `DATE_ATOM`

Display formatters:

- `toStringDate()` => `Y-m-d`
- `toStringTime()` => `h:i A`
- `toStringDatetime()` => `Y-m-d h:i A`

Use API formatters for transport payloads and display formatters for human-facing strings.

## Global Helpers

Helpers in `Helpers.php`:

- `authUser()` - returns the authenticated user or throws if unauthenticated.
- `vue()` - Inertia response helper with merged page meta.
- `optionalProp()` - wraps `Inertia::optional()`.
- `deferProp()` - wraps `Inertia::defer()`.
- `alwaysProp()` - wraps `Inertia::always()`.

## Guidelines

- Keep this layer framework-oriented and lightweight.
- Do not place business workflows here; use actions/services for domain logic.
- Prefer typed return values and immutable date handling.
- Keep formatting logic centralized to avoid inconsistent date output.
