# Support Layer

This folder contains framework-level support utilities shared across the backend.

## Files

- `CarbonImmutable.php` - application date class extending CarbonImmutable.
- `Helpers.php` - global helper functions used by controllers and Inertia responses.
- `LaraTweaks.php` - centralized app boot tweaks and framework defaults.
- `UserAgent.php` - minimal user agent parser for display, such as the browser sessions list.

## LaraTweaks

`App\\Support\\LaraTweaks::default()` is called from `AppServiceProvider::boot()` and applies these defaults:

- Prohibit destructive DB commands in production.
- Force the HTTPS URL scheme when `APP_URL` uses HTTPS.
- Require strong, uncompromised passwords in production via `Password::defaults()`.
- Set `App\\Support\\CarbonImmutable` as the default Date implementation.
- Unguard models, and enable strict Eloquent behavior outside production.
- Disable default wrapping for JSON resources.
- Enable Vite prefetching with concurrency 3.

## CarbonImmutable

`App\\Support\\CarbonImmutable` is the app default date class.

API-safe formatters:

- `toApiDate()` => `Y-m-d`
- `toApiTime()` => `H:i:s`
- `toApiDatetime()` => `DATE_ATOM`

Display formatters:

- `toStringDate()` => `M j, Y`
- `toStringTime()` => `h:i A`
- `toStringDatetime()` => `M j, Y h:i A`

Use API formatters for transport payloads and display formatters for human-facing strings. Display formatters use the app timezone, so send `toApiDatetime()` and format in the browser when the user's timezone matters.

## Global Helpers

Helpers in `Helpers.php`:

- `authUser()` - returns the authenticated user or throws if unauthenticated.
- `vue()` - Inertia response helper with merged page meta.
- `optionalProp()` - wraps `Inertia::optional()`.
- `deferProp()` - wraps `Inertia::defer()`.
- `alwaysProp()` - wraps `Inertia::always()`.

## Customizing

This code belongs to your app, so change it directly:

- **Dates:** add methods to `CarbonImmutable` and every framework date gets them, including model timestamps:

    ```php
    public function toStringMonth(): string
    {
        return $this->format('F Y');
    }
    ```

    Change a format by editing its method. Keep the API formatters stable, because the frontend parses them.

- **Framework defaults:** add a static method to `LaraTweaks` and call it from `default()`, or remove a call to turn a default off.
- **Helpers:** add global functions to `Helpers.php`. They're autoloaded through `composer.json`.

Action, DTO, enum and resource conventions come from [laravibe-standards](https://github.com/thesoftpulze/laravibe-standards). It provides the `AsDTO` and `HasEnumMetadata` traits, the `AppResource` field helpers, and the `make:action` / `make:dto` generators. The stubs it generates from are published in `stubs/`, so edit them to change what new classes look like.

## Guidelines

- Keep this layer framework-oriented and lightweight.
- Do not place business workflows here; use actions/services for domain logic.
- Prefer typed return values and immutable date handling.
- Keep formatting logic centralized to avoid inconsistent date output.
