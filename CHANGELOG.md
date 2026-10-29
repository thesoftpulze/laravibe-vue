# Changelog

All notable changes to this starter kit are documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

Apps created from the kit do not receive updates through Composer. Use the
entries below to port changes into an existing app by hand. Entries marked
**Breaking** need manual migration.

## [Unreleased]

## [0.3.1] - 2026-10-29

### Changed

- The package description, keywords and homepage in `composer.json` match the GitHub repository.

## [0.3.0] - 2026-10-29

### Added

- Administration is limited to admins through an `is_admin` column and an `accessAdministration` gate, with a searchable, paginated users list. The seeder creates `admin@example.com` and `test@example.com` ([#34](https://github.com/thesoftpulze/laravibe-vue/pull/34)).
- Account overview at `/account` with email verification, two-factor and passkey status ([#35](https://github.com/thesoftpulze/laravibe-vue/pull/35)).
- Profile picture upload and removal on the Profile page, stored on the `public` disk and shown in the user menus. Run the new `avatar_path` migration and `php artisan storage:link` ([#38](https://github.com/thesoftpulze/laravibe-vue/pull/38)).
- Administration dashboard with user statistics and recent sign-ups, a ⌘K command palette, breadcrumbs, and user filters, sorting and row actions (role change, resend verification, delete) ([#41](https://github.com/thesoftpulze/laravibe-vue/pull/41)).
- Typed runtime settings in `app/Settings`, stored in a new `settings` table, with Administration → Settings pages for General, Authentication and Mail behind a `manageSettings` gate ([#42](https://github.com/thesoftpulze/laravibe-vue/pull/42)).
- Toast `description`, persistent toasts (`duration: 0`), a same-tab `visit` action, chainable `PendingToast` helpers, and `success`, `error`, `warning`, `info`, `promise` and `dismiss` on `useToast()` ([#40](https://github.com/thesoftpulze/laravibe-vue/pull/40)).
- Frontend and settings AI guidelines; the README documents `boost:install` ([#43](https://github.com/thesoftpulze/laravibe-vue/pull/43)).

### Changed

- **Breaking:** `is_admin` is added to the original users migration, so existing databases need `php artisan migrate:fresh --seed` ([#34](https://github.com/thesoftpulze/laravibe-vue/pull/34)).
- **Breaking:** the profile form moves to `/account/profile` (`account.profile`), and the `account/General` view is renamed to `account/Profile` ([#35](https://github.com/thesoftpulze/laravibe-vue/pull/35)).
- **Breaking:** one `AppLogo` component replaces `Logo` and `LogoIcon` ([#36](https://github.com/thesoftpulze/laravibe-vue/pull/36)).
- **Breaking:** a single toaster is mounted by `registerToaster()` outside the page tree; layouts no longer mount `ToastProvider` ([#40](https://github.com/thesoftpulze/laravibe-vue/pull/40)).
- TheSoftPulze brand theme, fonts, L-wave logo and favicons; shadcn-vue components updated to the `new-york` style ([#36](https://github.com/thesoftpulze/laravibe-vue/pull/36)).
- The home page is a single-screen welcome with an install command switcher ([#39](https://github.com/thesoftpulze/laravibe-vue/pull/39)).
- Toasts sit bottom-right on desktop and top-center on phones, with per-type default durations ([#40](https://github.com/thesoftpulze/laravibe-vue/pull/40)).
- A collapsed administration sidebar peeks on hover without shifting the layout ([#41](https://github.com/thesoftpulze/laravibe-vue/pull/41)).
- The `verified` middleware alias follows the "Require a verified email address" setting ([#42](https://github.com/thesoftpulze/laravibe-vue/pull/42)).

### Fixed

- The passkey sign-in button no longer spins while passkey autofill waits ([#37](https://github.com/thesoftpulze/laravibe-vue/pull/37)).
- Toasts are no longer dropped when the layout changes ([#40](https://github.com/thesoftpulze/laravibe-vue/pull/40)).

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

[Unreleased]: https://github.com/thesoftpulze/laravibe-vue/compare/v0.3.1...HEAD
[0.3.1]: https://github.com/thesoftpulze/laravibe-vue/compare/v0.3.0...v0.3.1
[0.3.0]: https://github.com/thesoftpulze/laravibe-vue/compare/v0.2.0...v0.3.0
[0.2.0]: https://github.com/thesoftpulze/laravibe-vue/compare/v0.1.1...v0.2.0
[0.1.1]: https://github.com/thesoftpulze/laravibe-vue/compare/v0.1.0...v0.1.1
[0.1.0]: https://github.com/thesoftpulze/laravibe-vue/compare/v0.0.2...v0.1.0
[0.0.2]: https://github.com/thesoftpulze/laravibe-vue/compare/v0.0.1...v0.0.2
[0.0.1]: https://github.com/thesoftpulze/laravibe-vue/compare/v0.0.0...v0.0.1
[0.0.0]: https://github.com/thesoftpulze/laravibe-vue/releases/tag/v0.0.0
