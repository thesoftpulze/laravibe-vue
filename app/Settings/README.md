# Settings

## Overview

Settings are values an administrator can change while the application runs, such as the site name. They live in the `settings` table, grouped into typed classes.

- Each group is a class extending `App\Settings\Settings`. Every public property is one setting.
- A setting keeps the default from its class until a different value is saved. Only values that differ from their defaults are stored, so a default changed in code reaches every installation that never changed it.
- Every stored value is read with one query and cached forever under the `settings` key. Saving a group clears the cache.
- Until the `settings` table exists, for example before the first migration, every group has its defaults.

Use config and `.env` for values that are fixed per environment, such as credentials and infrastructure. Use settings for values that the people running the application change.

## Adding a group

1. Create a class in `app/Settings`:

```php
final class BillingSettings extends Settings
{
    public string $currency = 'usd';

    public int $trialDays = 14;

    public BillingInterval $defaultInterval = BillingInterval::MONTHLY;

    #[Encrypted]
    public ?string $stripeSecret = null;

    public static function group(): string
    {
        return 'billing';
    }
}
```

2. Register it under `groups` in `config/settings.php`.

That is all. A new setting needs no migration.

### Rules

- Every setting needs a default value.
- Supported types are `string`, `int`, `float`, `bool`, `array`, `mixed` and backed enums, each optionally nullable.
- Mark secrets with `#[Encrypted]`. They are stored encrypted with `APP_KEY`, so rotating the key resets them to their defaults.
- A stored value that no longer fits its property, for example after a type change, is ignored and the default applies.
- Never rename a group's `group()` or a property without moving its stored rows; the old values would be ignored.

## Reading settings

Inject the group. It is loaded once per request (and per job with Octane or queues):

```php
public function show(BillingSettings $billing): Response
{
    return vue('billing/Show', ['trialDays' => $billing->trialDays]);
}
```

Outside of injection, use `app(BillingSettings::class)`.

## Saving settings

Fill the group with validated data and save it through `SettingsRepository`, usually from an action:

```php
final readonly class UpdateBillingSettingsAction
{
    public function __construct(
        private SettingsRepository $settings,
    ) {}

    public function handle(UpdateBillingSettingsDTO $data): void
    {
        $this->settings->save(app(BillingSettings::class)->fill($data->toArray()));
    }
}
```

- `fill()` throws an `InvalidArgumentException` for a name the group does not have, and a `TypeError` for a value of the wrong type. Pass enums as cases, not their values.
- `save()` makes the new values visible for the rest of the request, including their config overrides.
- After editing the `settings` table by hand, run `php artisan cache:forget settings`.

## Overriding config

A group can override config, so code that reads config (mail, framework packages) follows the setting. Implement `OverridesConfig` and return the overrides from `config()`. A null value keeps the value from config and `.env`:

```php
final class GeneralSettings extends Settings implements OverridesConfig
// ...

public function config(): array
{
    return ['app.name' => $this->siteName];
}
```

Overrides are applied when the application boots and again whenever the group is saved. `config:cache` and `optimize` skip them, so the cached config keeps the `.env` values.

## Testing

Save settings through the repository so the cache and container stay in step:

```php
app(SettingsRepository::class)->save(new GeneralSettings()->fill(['siteName' => 'Acme']));
```

## Groups

| Group | Class | Settings |
| --- | --- | --- |
| `general` | `GeneralSettings` | `siteName` (overrides `app.name`), `tagline`, `supportEmail` |
| `authentication` | `AuthenticationSettings` | `allowRegistration` (closes Fortify's registration routes), `requireEmailVerification` (switches the `verified` middleware) |
| `mail` | `MailSettings` | `fromName`, `fromAddress` (override `mail.from`) |

Each group has a page under Administration → Settings. To give a new group a page, add a controller, form request, DTO and update action like the existing ones, then add the page to `resources/layouts/AdministrationSettingsLayout.vue`.
