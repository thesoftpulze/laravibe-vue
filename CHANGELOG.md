# Changelog

All notable changes to this starter kit are documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

Apps created from the kit do not receive updates through Composer. Use the
entries below to port changes into an existing app by hand. Entries marked
**Breaking** need manual migration.

## [Unreleased]

### Added

- Profile picture upload and removal on the Profile page, stored on the `public` disk and shown in the user menus. Run the new `avatar_path` migration and `php artisan storage:link`.

## [0.2.0] - 2026-10-27

### Added

- Two-factor authentication through Fortify: QR code and setup key, code confirmation, recovery codes, and a challenge page with its own rate limiter ([#29](https://github.com/thesoftpulze/laravibe-vue/pull/29)).
- Browser session list on the Security page, with logout of other sessions behind a password check ([#29](https://github.com/thesoftpulze/laravibe-vue/pull/29)).
- Passkey sign-in with autofill, passkey password confirmation, and passkey management on the Security page ([#30](https://github.com/thesoftpulze/laravibe-vue/pull/30)).
- Inertia error page for 403, 404, 419, 500 and 503 responses; expired form submissions redirect back ([#31](https://github.com/thesoftpulze/laravibe-vue/pull/31)).
- Global security headers middleware (nosniff, frame, referrer, permissions and opener policies, HSTS over HTTPS in production) ([#31](https://github.com/thesoftpulze/laravibe-vue/pull/31)).
- `AuthenticateSession` middleware; changing the password logs out other devices ([#28](https://github.com/thesoftpulze/laravibe-vue/pull/28)).
- `routes/console.php` with auth and queue pruning schedules ([#28](https://github.com/thesoftpulze/laravibe-vue/pull/28)).
- GitHub Actions CI and Dependabot ([#26](https://github.com/thesoftpulze/laravibe-vue/pull/26)).
- MIT `LICENSE` file ([#33](https://github.com/thesoftpulze/laravibe-vue/pull/33)).

### Changed

- **Breaking:** the package is renamed from `softpulze/laravibe-vue` to `thesoftpulze/laravibe-vue`.
- **Breaking:** authentication runs on Laravel Fortify instead of hand-rolled controllers ([#26](https://github.com/thesoftpulze/laravibe-vue/pull/26)).
- **Breaking:** DTOs, enums, resources and `make:dto` / `make:action` come from `thesoftpulze/laravibe-standards` (now `^0.7.1`); stubs live in `stubs/` ([#26](https://github.com/thesoftpulze/laravibe-vue/pull/26), [#32](https://github.com/thesoftpulze/laravibe-vue/pull/32)).
- **Breaking:** resource timestamps serialize as `Y-m-d H:i:s` without `*_display` fields ([#26](https://github.com/thesoftpulze/laravibe-vue/pull/26)).
- **Breaking:** toasts are delivered as Inertia flash data instead of a `toasts` shared prop ([#26](https://github.com/thesoftpulze/laravibe-vue/pull/26)).
- **Breaking:** frontend tooling moves to vite-plus (`vp build/check/lint/fmt`), replacing ESLint and Prettier ([#26](https://github.com/thesoftpulze/laravibe-vue/pull/26)).
- **Breaking:** Wayfinder generates into `resources/` and its output is no longer tracked ([#27](https://github.com/thesoftpulze/laravibe-vue/pull/27)).
- Upgraded to the Laravel 13 starter kit baseline: Laravel 13, Inertia 3.5, Pest 5 / PHPUnit 13, `laravel/pao` ([#26](https://github.com/thesoftpulze/laravibe-vue/pull/26)).
- Models stay unguarded: `$fillable` is removed and `fill()` replaces `forceFill()` ([#27](https://github.com/thesoftpulze/laravibe-vue/pull/27)).
- Fortify home points at `/account` ([#27](https://github.com/thesoftpulze/laravibe-vue/pull/27)).
- Shared abilities resolve from gates, and account form requests authorize with the same gates ([#28](https://github.com/thesoftpulze/laravibe-vue/pull/28)).
- The verification email is resent when the email address changes ([#28](https://github.com/thesoftpulze/laravibe-vue/pull/28)).
- The Security page sits behind password confirmation ([#29](https://github.com/thesoftpulze/laravibe-vue/pull/29)).
- Strict models apply outside production only; HTTPS is forced when `APP_URL` uses `https` ([#33](https://github.com/thesoftpulze/laravibe-vue/pull/33)).
- Display date formats are more readable, and tests block stray HTTP requests and disable SSR ([#33](https://github.com/thesoftpulze/laravibe-vue/pull/33)).
- The README and AI guidelines are trimmed to what is specific to this kit ([#33](https://github.com/thesoftpulze/laravibe-vue/pull/33)).

### Removed

- **Breaking:** Laravel Sail ([#33](https://github.com/thesoftpulze/laravibe-vue/pull/33)).
- The `/dashboard` redirect ([#27](https://github.com/thesoftpulze/laravibe-vue/pull/27)).
- The shared inspiring quote, the deprecated `useAuth().authUser` alias and the unserialized `avatar` field ([#28](https://github.com/thesoftpulze/laravibe-vue/pull/28)).
- The local action, DTO, enum and resource READMEs, in favor of the laravibe-standards docs ([#32](https://github.com/thesoftpulze/laravibe-vue/pull/32)).
- Boost-generated agent files from version control ([#26](https://github.com/thesoftpulze/laravibe-vue/pull/26)).

## [0.1.1] - 2026-05-05

### Fixed

- Wayfinder output is no longer tracked, and `.gitignore` is reorganized.
- Formatting of the `autoload-dev` section in `composer.json`.

## [0.1.0] - 2026-04-28

### Added

- Local DTO (`AsDTO`), enum (`HasEnumMetadata`) and API resource (`AppResource`, `AppResourceCollection`) conventions.
- Shared auth abilities with tests for shared auth props.
- Toast metadata support, strict hydration and queue management.
- Laravel Boost, Laravel IDE Helper and `nunomaduro/pao`, with AI agent guidelines.
- Hover-to-expand collapsed sidebar and a `SidebarRail` component.
- `logs:delete` task to clear storage logs.
- `APP_TIMEZONE` environment variable.

### Changed

- Upgraded to Laravel 13 and Inertia v3 ([#25](https://github.com/thesoftpulze/laravibe-vue/pull/25)).
- Account management UI redesigned; settings renamed to account ([#22](https://github.com/thesoftpulze/laravibe-vue/pull/22), [#23](https://github.com/thesoftpulze/laravibe-vue/pull/23)).
- Theme variables normalized and color tokens updated.
- Header navigation and user menu simplified; social links removed ([#24](https://github.com/thesoftpulze/laravibe-vue/pull/24)).
- shadcn components updated.
- Explicit return types and `#[Override]` attributes added across the codebase.

### Removed

- The `Controller` base class.
- Old lint and test CI workflows.

## [0.0.2] - 2025-09-27

### Changed

- **Breaking:** the package is renamed from `softpulselab/laravibe-vue` to `softpulze/laravibe-vue` ([#20](https://github.com/thesoftpulze/laravibe-vue/pull/20)).

## [0.0.1] - 2025-09-20

### Changed

- Rebuilt the toast system as one unified implementation ([#19](https://github.com/thesoftpulze/laravibe-vue/pull/19)).
- Seeder events are suppressed during seeding.
- Bumped Composer dependencies.

## [0.0.0] - 2025-09-02

### Added

- Initial release of LaraVibe Vue as `softpulselab/laravibe-vue`.
- Dashboard, Account and Guest persistent layouts, with card and header layouts for auth and app pages.
- Account pages and the account URL namespace.
- `useAuth` composable, utility types and the `isCallable` type guard.
- `make:action` and `make:data` Artisan commands with stubs.
- `AppTweaks` for app defaults, and a custom immutable Carbon wrapper as the default date class.
- Global helpers for Inertia props.
- Pint configuration, LaraVibe logo components and favicon.

### Changed

- Frontend files live in `resources/` instead of `resources/js/`.

[Unreleased]: https://github.com/thesoftpulze/laravibe-vue/compare/v0.2.0...HEAD
[0.2.0]: https://github.com/thesoftpulze/laravibe-vue/compare/v0.1.1...v0.2.0
[0.1.1]: https://github.com/thesoftpulze/laravibe-vue/compare/v0.1.0...v0.1.1
[0.1.0]: https://github.com/thesoftpulze/laravibe-vue/compare/v0.0.2...v0.1.0
[0.0.2]: https://github.com/thesoftpulze/laravibe-vue/compare/v0.0.1...v0.0.2
[0.0.1]: https://github.com/thesoftpulze/laravibe-vue/compare/v0.0.0...v0.0.1
[0.0.0]: https://github.com/thesoftpulze/laravibe-vue/releases/tag/v0.0.0
