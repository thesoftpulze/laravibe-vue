<div align="center">

# LaraVibe-Vue

**A Laravel + Inertia.js + Vue 3 starter kit.**

[![PHP](https://img.shields.io/badge/PHP-8.4-777BB4?style=flat-square&logo=php)](https://php.net)
[![Laravel](https://img.shields.io/badge/Laravel-13-FF2D20?style=flat-square&logo=laravel)](https://laravel.com)
[![Vue](https://img.shields.io/badge/Vue-3-4FC08D?style=flat-square&logo=vue.js)](https://vuejs.org)
[![Inertia](https://img.shields.io/badge/Inertia.js-3-9553E9?style=flat-square)](https://inertiajs.com)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind-4-06B6D4?style=flat-square&logo=tailwindcss)](https://tailwindcss.com)
[![License](https://img.shields.io/badge/License-MIT-brightgreen?style=flat-square)](LICENSE)

Made by [The SoftPulze](https://softpulze.com)

</div>

## Stack

- **Backend:** Laravel 13, Inertia.js 3, Laravel Fortify, Laravel Wayfinder, [laravibe-standards](https://github.com/thesoftpulze/laravibe-standards)
- **Frontend:** Vue 3, TypeScript, Tailwind CSS 4, shadcn-vue (Reka UI), VueUse, Lucide, vue-sonner
- **Tooling:** Pest 5, Larastan, Pint, Rector, Vite+ (Oxlint, Oxfmt), vue-tsc, Laravel Boost, Pail

## Features

- **Authentication** (Fortify): registration, login, password reset, email verification, password confirmation, two-factor authentication with recovery codes, and passkeys.
- **Account pages:** profile, security (password, 2FA, passkeys, browser sessions) and appearance (light/dark/system).
- **Administration area** at `/administration`, protected by middleware.
- **Inertia error pages** for 403, 404, 419, 500 and 503, plus security headers on every response.
- **SSR** through `@inertiajs/vite`.
- **Toasts** sent as Inertia flash data, so they show once and never replay from history. See [app/Toast/README.md](app/Toast/README.md).

    ```php
    toastSuccess('Profile updated.');
    ```

- **`useAuth` composable** with `user`, `requireUser()`, `can()` and `isAuthenticated`. Abilities are resolved on the server. See [resources/composables/README.md](resources/composables/README.md).

## Installation

Requires PHP 8.4+ and Node.js 22+.

```bash
laravel new my-app --using=thesoftpulze/laravibe-vue
# or: composer create-project thesoftpulze/laravibe-vue my-app

cd my-app
npm install
php artisan boost:install
composer dev
```

From a git clone, run `composer setup` and then `php artisan boost:install`. `composer setup` installs dependencies, creates `.env`, generates the key, migrates and builds assets. `boost:install` generates the files for your AI agents (see [AI Agents](#ai-agents)).

`composer dev` starts Vite, the queue listener and Pail. Serve the app with Herd or `php artisan serve`.

## AI Agents

The app is set up for AI agents with [Laravel Boost](https://github.com/laravel/boost). Agent files aren't committed. Each developer generates them for the agents they use:

```bash
php artisan boost:install
```

Pick your agents, keep guidelines, skills and the MCP server enabled, and select **thesoftpulze/laravibe-standards** when prompted for third-party packages. This writes `boost.json` and, depending on your agents, `CLAUDE.md`, `AGENTS.md`, `.mcp.json`, `opencode.json` and the skills folders (`.claude/skills`, `.agents/skills`). All of them are gitignored.

- **Committed:** `.ai/guidelines` (auth, frontend, settings, support, toasts). Boost combines them with its Laravel, Inertia, Vue, Tailwind, Pest and Wayfinder guidelines and the action, DTO, enum and resource guidelines from laravibe-standards.
- **MCP server:** `php artisan boost:mcp` gives agents documentation search, the database schema, read-only queries, browser logs and the last error. Restart your agent after installing so it picks the server up.
- **Keeping it current:** run `php artisan boost:update` after updating packages or pulling changes to `.ai/guidelines`.
- **Adding your own:** put guidelines in `.ai/guidelines/*.md` and skills in `.ai/skills/`, then run `boost:update`. To record your app's own conventions in `.ai/rules`, ask your agent to run the `infer-conventions` skill.

## Conventions

- Frontend code lives directly in `resources/` (`components/`, `composables/`, `layouts/`, `views/`). There is no `resources/js`.
- Actions, DTOs, enums and resources follow [laravibe-standards](https://github.com/thesoftpulze/laravibe-standards), which provides `make:action`, `make:dto` and the `make:enum` / `make:resource` stubs.
- Models are unguarded. Pass only validated or explicitly built arrays to `create()`, `fill()` and `update()`.
- Framework defaults live in `App\Support\LaraTweaks`, and dates use `App\Support\CarbonImmutable`. See [app/Support/README.md](app/Support/README.md) to customize them.
- Wayfinder output (`resources/actions`, `resources/routes`, `resources/wayfinder`) is generated during `composer dev` and builds, and is gitignored. The app overrides `wayfinder:generate` to write to `resources/`, so don't pass `--path`.

## Scripts

| Command                            | Description                                           |
| ---------------------------------- | ----------------------------------------------------- |
| `composer dev`                     | Start the dev processes                               |
| `composer test`                    | Run the test suite                                    |
| `composer format` / `format:check` | Pint + Vite+ fmt                                      |
| `composer lint` / `lint:check`     | Rector + Vite+ lint                                   |
| `composer types:check`             | Larastan + vue-tsc                                    |
| `composer review`                  | Lint, format, type checks and tests (the same as CI)  |
| `composer build` / `build:ssr`     | Build frontend assets, optionally with the SSR bundle |
| `composer deps:update`             | Update Composer and npm packages                      |

## Credits

LaraVibe-Vue is developed and maintained by [The SoftPulze](https://softpulze.com).

- [Ashok Barua](https://ashokbaruaakas.com), creator and maintainer
- [All contributors](https://github.com/thesoftpulze/laravibe-vue/graphs/contributors)

Built on [Laravel](https://laravel.com), [Inertia.js](https://inertiajs.com), [Vue](https://vuejs.org) and [shadcn-vue](https://www.shadcn-vue.com), and inspired by the official Laravel starter kits.

## License

LaraVibe-Vue is open-source software licensed under the [MIT license](LICENSE). Copyright © 2025-present The SoftPulze.
