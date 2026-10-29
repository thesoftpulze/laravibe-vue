# Settings Conventions for AI Agents

## Scope

Use this guide when adding, reading, or saving runtime settings, or when building their administration pages. Full details live in `app/Settings/README.md`.

## Settings vs Config Rules

- Use settings for values the people running the application change at runtime, such as the site name.
- Use config and `.env` for values fixed per environment, such as credentials and infrastructure.

## Group Rules

- Each group is a class in `app/Settings` extending `App\Settings\Settings`, with a static `group()` key. Every public property is one setting.
- Register every group under `groups` in `config/settings.php`.
- Give every setting a default value. Supported types are `string`, `int`, `float`, `bool`, `array`, `mixed` and backed enums, each optionally nullable.
- Mark secrets with `#[Encrypted]`.
- A new setting needs no migration. Never rename `group()` or a property without moving its stored rows.
- To make config follow a setting, implement `OverridesConfig` and return the overrides from `config()`. A null value keeps the config value.

## Reading Rules

- Inject the group class (e.g. `GeneralSettings $settings`), or use `app(GeneralSettings::class)` outside injection.
- Do not query the `settings` table directly.

## Saving Rules

- Save through `SettingsRepository::save()` from an action, filling the group with DTO data: `$this->settings->save(app(GeneralSettings::class)->fill($data->toArray()))`.
- Pass enums to `fill()` as cases, not their values.
- Never write the `settings` table or the `settings` cache key directly.

## Administration Page Rules

- Each group gets a page under Administration → Settings, built like the existing ones: a controller in `App\Http\Controllers\Administration\Settings`, a form request in `App\Http\Requests\Administration\Settings`, a DTO in `App\DTOs\Administration`, and an update action in `App\Actions\Administration`.
- Register its routes inside the `can:manageSettings` group in `routes/web.php`, put the page in `resources/views/administration/settings`, and add it to `resources/layouts/AdministrationSettingsLayout.vue`.

## Testing Rules

- Set settings in tests with `app(SettingsRepository::class)->save(new GeneralSettings()->fill([...]))` so the cache and container stay in step.
