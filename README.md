<div align="center">

# LaraVibe-Vue

**A modern Laravel + Inertia.js + Vue 3 starter kit — skip the boilerplate, build your app.**

[![PHP](https://img.shields.io/badge/PHP-8.4-777BB4?style=flat-square&logo=php)](https://php.net)
[![Laravel](https://img.shields.io/badge/Laravel-13-FF2D20?style=flat-square&logo=laravel)](https://laravel.com)
[![Vue](https://img.shields.io/badge/Vue-3-4FC08D?style=flat-square&logo=vue.js)](https://vuejs.org)
[![Inertia](https://img.shields.io/badge/Inertia.js-3-9553E9?style=flat-square)](https://inertiajs.com)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind-4-06B6D4?style=flat-square&logo=tailwindcss)](https://tailwindcss.com)
[![License](https://img.shields.io/badge/License-MIT-brightgreen?style=flat-square)](LICENSE)

</div>

---

## Introduction

LaraVibe-Vue is an open-source starter kit that combines the backend power of **Laravel 13** with a fully typed **Vue 3** frontend via **Inertia.js v3** — giving you a modern SPA feel without sacrificing the simplicity of classic server-side routing and controllers.

Out of the box you get authentication, account management, an administration area, SSR support, type-safe route helpers, and a curated set of developer tooling — all wired together and ready to go.

---

## Tech Stack

Every package in this stack was chosen deliberately. Nothing is here for show — each one earns its place by solving a real problem well.

### Core

The backbone. These three working together is what makes LaraVibe-Vue feel different from a traditional Laravel app or a standalone SPA.

| Package                             | Version | Why it's here                                                                               |
| ----------------------------------- | ------- | ------------------------------------------------------------------------------------------- |
| [Laravel](https://laravel.com)      | 13      | The application framework — routing, ORM, auth, jobs, mail, and everything in between       |
| [Inertia.js](https://inertiajs.com) | 3       | The bridge — write server-side controllers, get a full SPA on the frontend, no API required |
| [Vue 3](https://vuejs.org)          | 3       | Reactive UI with the Composition API — clean, typed, and a joy to work in                   |

### Frontend

| Package                                      | Version | Why it's here                                                                             |
| -------------------------------------------- | ------- | ----------------------------------------------------------------------------------------- |
| [Tailwind CSS](https://tailwindcss.com)      | 4       | Utility-first styling — design directly in your markup, no context switching              |
| [Reka UI](https://reka-ui.com)               | 2       | Headless, accessible UI primitives — the foundation for the bundled shadcn-vue components |
| [VueUse](https://vueuse.org)                 | 12      | Hundreds of battle-tested Vue composables so you don't write them from scratch            |
| [TypeScript](https://www.typescriptlang.org) | 5       | End-to-end type safety across your entire frontend codebase                               |
| [Vite+](https://viteplus.dev)                | 1       | Vite-powered dev server and builds plus integrated lint, format, and type-aware checks    |
| [Lucide Vue](https://lucide.dev)             | 1       | Clean, consistent icon library (`@lucide/vue`) with tree-shaking out of the box           |
| [vue-sonner](https://vue-sonner.vercel.app)  | 2       | The Vue renderer behind the built-in toast notification system                            |

### Developer Experience & Tooling

| Package                                                                  | Version | Why it's here                                                                                  |
| ------------------------------------------------------------------------ | ------- | ---------------------------------------------------------------------------------------------- |
| [Laravel Wayfinder](https://github.com/laravel/wayfinder)                | 0.x     | Auto-generates TypeScript route functions from your PHP controllers — no hardcoded URLs, ever  |
| [Pest](https://pestphp.com)                                              | 5       | The most expressive PHP testing framework available today                                      |
| [Larastan](https://github.com/larastan/larastan)                         | 3       | PHPStan tuned for Laravel — catches type errors and N+1 risks before they ship                 |
| [Laravel Pint](https://laravel.com/docs/pint)                            | 1       | Opinionated PHP formatter — your code style is decided, consistently, forever                  |
| [Rector](https://getrector.com)                                          | 2       | Automated PHP refactoring and upgrade rules — future-proofs your codebase                      |
| [Laravel Fortify](https://laravel.com/docs/fortify)                      | 1       | Headless authentication backend — login, registration, password reset, email verification, 2FA |
| [LaraVibe Standards](https://github.com/thesoftpulze/laravibe-standards) | 0.x     | Shared DTO, enum, and API resource conventions with matching generators and AI guidelines      |
| [Laravel Pail](https://github.com/laravel/pail)                          | 1       | Real-time log tailing right in your terminal — no more `tail -f storage/logs/laravel.log`      |
| [Laravel Sail](https://laravel.com/docs/sail)                            | 1       | Docker environment for teams that prefer containers over local installs                        |

---

## Features

LaraVibe-Vue isn't just a scaffold — it's a carefully assembled foundation that solves the boring problems so you can focus on what makes your product unique.

### For Your Users

#### 🔐 Authentication — Complete, Out of the Box

Full auth flows, ready on day one, powered by **Laravel Fortify** — the same backend the official Laravel starter kits use. No wiring up forms manually.

- **Registration & login** with email/password
- **Password reset** via email — the full flow, already working, without revealing which emails have accounts
- **Email verification** — prompt users to verify before accessing protected areas
- **Confirmable password (sudo mode)** — re-prompt for the password before sensitive operations like deleting an account or changing security settings
- **Two-factor authentication** — TOTP via any authenticator app, with QR code setup, confirmation, and one-time recovery codes

#### 👤 Account Management — The Pages Users Expect

Users expect to manage their own profile. LaraVibe-Vue ships those pages pre-built so you don't have to.

- **Profile** — update name and email address, with email re-verification on change
- **Security** — behind password confirmation: change password (signs out your other devices), manage two-factor authentication, and review or log out your other browser sessions (database session driver)
- **Appearance** — light, dark, and system theme switcher that persists across sessions

#### 🛡️ Administration Area — Protected and Ready to Extend

A dedicated `/administration` section, guarded by middleware, with a dashboard page waiting for your content. Add your admin features without worrying about route structure or access control from scratch.

---

### For Developers

#### 🤖 AI-Ready from Day One

LaraVibe-Vue ships with **Laravel Boost** integration, providing AI agents with complete codebase context — schema inspection, database queries, real-time error logs, and semantic documentation search.

Agent files are generated per developer, not committed, so everyone picks the agents they actually use. After cloning, run:

```bash
php artisan boost:install
```

When prompted for third-party packages, select **thesoftpulze/laravibe-standards**. Boost then writes `AGENTS.md` / `CLAUDE.md`, skills, and MCP config for your chosen agents. These combine the project guidelines in `.ai/guidelines` (actions, auth, support, toasts) with the DTO, enum, and resource guidelines and skills shipped by `thesoftpulze/laravibe-standards`. Run `php artisan boost:update` after updating packages to refresh them; it prompts for any newly installed packages that ship guidelines.

#### 🛠️ Developer Tooling — Full Power, Zero Configuration

Most starter kits leave you to wire up your own code quality tools. LaraVibe-Vue ships with a **complete, pre-configured professional toolchain** — the same setup senior engineers spend days assembling on real projects, ready to go from your very first commit.

| Tool                       | What it does                                                                              |
| -------------------------- | ----------------------------------------------------------------------------------------- |
| **Pest 5**                 | Expressive, readable PHP tests — the best testing experience in the Laravel ecosystem     |
| **Larastan (PHPStan max)** | Catches bugs before they reach production — static analysis tuned for Laravel             |
| **Laravel Pint**           | Zero-config PHP code formatter — your code always looks clean, always                     |
| **Rector**                 | Automated PHP refactoring and upgrades — keeps your codebase modern without manual effort |
| **Vite+ lint (Oxlint)**    | Fast, type-aware linting for TypeScript and Vue files                                     |
| **Vite+ fmt (Oxfmt)**      | Frontend formatting with Tailwind class sorting — consistent across `.vue` and `.ts`      |
| **vue-tsc**                | Full type checking for Vue single-file components                                         |

Everything is wired into composer scripts so the whole team uses the same workflow, and GitHub Actions runs the same checks on every push and pull request. One command before you push — `composer review` — and you know your code is clean, typed, formatted, and tested. No arguments, no setup, no excuses.

> New team member? They get the full toolchain the moment they clone the repo. Senior dev? Nothing to configure. It's already right.

#### ⚡ Type-Safe Routing with Laravel Wayfinder

Tired of hardcoded URLs breaking silently? **Wayfinder auto-generates fully-typed TypeScript functions** for every controller action and named route — straight from your PHP code into your Vue components. It runs automatically during `composer dev` and `npm run build`, so your frontend and backend are always in sync.

```vue
<!-- No more magic strings. Just types. -->
<Link :href="AccountController.edit().url()">My Account</Link>
```

#### 🖥️ SSR Out of the Box

Server-side rendering is configured and ready. Powered by `@inertiajs/vite` in cluster mode, your app serves fast initial HTML to every visitor — great for SEO and perceived performance — without any extra setup on your part.

#### 🎨 shadcn-vue UI Components

Pre-configured via `components.json` with **Reka UI** as the headless base. Beautiful, accessible, unstyled-by-default components you can drop in and style with Tailwind. Add new components with a single command:

```bash
npx shadcn-vue@latest add button
```

#### 🍞 Toast Notifications — Architected, Not Hacked In

The toast system is a first-class citizen, not an afterthought. It has its own service layer, typed DTOs, and a Vue renderer that handles every case. Toasts travel as Inertia flash data, so they show exactly once and never replay from browser history. Trigger toasts from anywhere on the backend with a single helper:

```php
toastSuccess('Profile updated.');
toastError('Something went wrong.');
```

See [app/Toast/README.md](app/Toast/README.md) for the full architecture and usage guide.

#### 🔑 Auth Composable — No Prop Drilling

Access the authenticated user anywhere in Vue without passing props down the tree. The `useAuth` composable gives you everything you need:

```ts
const { user, isAuthenticated, can, requireUser } = useAuth()
```

- `user` — the current user or `null` for guests
- `requireUser()` — non-null user on protected pages, no optional-chaining noise
- `can('publishPost')` — UI gating backed by real server-side ability checks
- `isAuthenticated` — boolean flag for conditional rendering

See [resources/composables/README.md](resources/composables/README.md) for the full guide.

#### 🧱 Structured Architecture You'll Actually Enjoy

Every part of the app has a clear home and a convention to follow — documented with READMEs so your whole team stays consistent:

| Layer              | Purpose                                                | Docs                                   |
| ------------------ | ------------------------------------------------------ | -------------------------------------- |
| **Actions**        | Single-responsibility business operations              | [README](app/Actions/README.md)        |
| **DTOs**           | Typed data transfer objects (`PageMeta`, `BreadCrumb`) | [README](app/DTOs/README.md)           |
| **Enums**          | Backed enums with shared behavior                      | [README](app/Enums/README.md)          |
| **HTTP Resources** | Eloquent serialization for API & Inertia               | [README](app/Http/Resources/README.md) |

DTO, enum, and resource conventions come from the [`thesoftpulze/laravibe-standards`](https://github.com/thesoftpulze/laravibe-standards) package, which also provides `make:dto` and the published `make:enum` / `make:resource` stubs.
| **Support** | Framework utilities and helpers | [README](app/Support/README.md) |
| **Toast** | Self-contained notification system | [README](app/Toast/README.md) |

### Project Structure

```
app/
├── Actions/            # Single-responsibility actions (incl. Fortify actions)
├── Concerns/           # Shared validation rules (password, profile)
├── DTOs/               # Typed data transfer objects (PageMeta, BreadCrumb)
├── Enums/              # Backed enums using HasEnumMetadata
├── Http/
│   ├── Controllers/    # Account controllers (auth is handled by Fortify)
│   ├── Middleware/     # Custom middleware
│   ├── Requests/       # Form request validation
│   ├── Resources/      # Eloquent API resources
│   └── Responses/      # Fortify response overrides
├── Models/             # Eloquent models
├── Providers/          # App and Fortify service providers
├── Support/            # Framework support utilities
└── Toast/              # Self-contained toast notification system

resources/
├── actions/            # Auto-generated Wayfinder controller actions (gitignored)
├── components/         # Shared Vue components & shadcn-vue UI
├── composables/        # Vue composables (useAuth, useAppearance, …)
├── layouts/            # Page layouts (Account, Auth, Guest, Administration)
├── routes/             # Auto-generated Wayfinder named routes (gitignored)
├── views/              # Inertia pages (auth/, account/, administration/)
├── wayfinder/          # Auto-generated Wayfinder runtime helper (gitignored)
└── app.ts              # Frontend entry point
```

---

## Requirements

- PHP **8.4+**
- Composer
- Node.js **22+** & npm
- SQLite / MySQL / PostgreSQL

---

## Installation

### 1. Create the project

If you have the [Laravel CLI installer](https://laravel.com/docs/installation#the-laravel-installer) installed, this is the fastest way to get started:

```bash
laravel new my-app --using=softpulze/laravibe-vue
cd my-app
```

Prefer Composer? This works equally well:

```bash
composer create-project softpulze/laravibe-vue my-app
cd my-app
```

Or clone the repository directly:

```bash
git clone https://github.com/softpulze/laravibe-vue.git my-app
cd my-app
cp .env.example .env
composer install
php artisan key:generate
```

### 2. Configure your environment

Edit `.env` and set your database connection (SQLite is the default):

```env
DB_CONNECTION=sqlite
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_DATABASE=laravibe
# DB_USERNAME=root
# DB_PASSWORD=
```

Set your application URL and mail settings as needed.

### 3. Run migrations

```bash
php artisan migrate
```

### 4. Install frontend dependencies

```bash
npm install
```

### 5. Start the development server

```bash
composer dev
```

This runs `php artisan dev`, which starts the dev processes (Vite, queue listener, Pail log watcher) together.

If you are using Laravel Herd, open your local Herd URL (for example: `https://my-app.test`).
If you are not using Herd, run `php artisan serve` in another terminal.

---

## Available Scripts

Everything you need for your daily workflow is a single command away. No need to remember a chain of tools, flags, or file paths — just use the script that matches what you're doing right now.

### Starting development

```bash
composer dev
```

This is the only command you'll need to kick off your day. It runs `php artisan dev`, starting the **Vite dev server**, a **queue listener**, and **Pail log watcher** — all at once, in a single terminal. Open your browser and start building.

Setting up a fresh clone? `composer setup` installs dependencies, creates `.env`, generates the key, migrates, and builds assets in one go.

> If you're on Laravel Herd, your site is already available at `https://my-app.test` — no extra server command needed.

### Running tests

```bash
composer test          # Run the full test suite
composer test:coverage # Run with coverage report
```

Or go straight to Pest:

```bash
composer pest          # Pest with compact output
composer pest:coverage # Pest with coverage
```

### Keeping code clean

You shouldn't have to think about formatting or style — these scripts handle it for you:

```bash
composer format        # Format PHP (Pint) + frontend (Vite+ fmt) in one go
composer format:check  # Check formatting without writing any changes
composer lint          # Auto-fix PHP (Rector) + JS/TS (Vite+ lint) issues
composer lint:check    # Check for lint issues without fixing
```

Need to target one tool specifically?

```bash
composer pint          # Format PHP code with Laravel Pint
composer pint:check    # Check PHP formatting only
composer phpstan       # Run Larastan static analysis
composer types:check   # Larastan + vue-tsc type checks
composer rector        # Apply Rector refactoring
composer rector:check  # Preview Rector changes without applying
npm run check          # Vite+ format + lint check for the frontend
npm run check:fix      # ...and fix what it can
```

### Pre-commit / CI review

Before pushing or opening a PR, run the full quality check in one command:

```bash
composer review
```

This is an alias of `composer ci:check`: **lint checks**, **format checks**, **static analysis and type checks**, and **tests** — everything that matters, in one pass. It's exactly what GitHub Actions runs, so if it passes locally, it'll pass in CI.

### Building for production

```bash
composer build         # Build frontend assets
composer build:ssr     # Build frontend + SSR bundle (recommended for production)
```

Or use npm directly if you prefer:

```bash
npm run build
npm run build:ssr
```

### Keeping dependencies fresh

```bash
composer deps:update        # Update both Composer and npm packages together
composer update:composer    # Update Composer packages only
composer update:npm         # Update npm packages only
```

### Miscellaneous

```bash
composer logs:delete # Wipe log files from storage/logs
```

---

## Using Laravel Wayfinder

Wayfinder is integrated with Vite and **auto-generates** typed TypeScript helpers for all your controllers and named routes whenever you run `composer dev` or `npm run build` — no manual step required.

Import and use them in your Vue components:

```ts
import AccountController from '@/actions/App/Http/Controllers/Account/AccountController'
import { home } from '@/routes'
```

```vue
<!-- In a template -->
<Link :href="AccountController.edit().url()">Account</Link>
```

The generated files live in `resources/actions`, `resources/routes` and `resources/wayfinder`, and are gitignored. To regenerate them manually, run `php artisan wayfinder:generate --with-form`. The app overrides this command so it writes to `resources/` by default; don't pass `--path`.

For the full API and advanced usage, refer to the [official Wayfinder documentation](https://github.com/laravel/wayfinder).

---

## Testing & Code Quality

Run the full test suite:

```bash
composer test
```

Filter to a specific test:

```bash
php artisan test --compact --filter=AccountController
```

Run the full quality review before pushing:

```bash
composer review
```

See [Available Scripts](#available-scripts) for the complete list of individual tool commands.

---

## Docker (Laravel Sail)

If you prefer Docker, Laravel Sail is included:

```bash
./vendor/bin/sail up -d
./vendor/bin/sail artisan migrate
./vendor/bin/sail npm run dev
```

---

## Deployment

LaraVibe-Vue can be deployed to any environment that supports PHP 8.4. For the fastest path to production, use [Laravel Cloud](https://cloud.laravel.com/).

Before deploying, build your frontend assets and run:

```bash
composer build:ssr        # build frontend + SSR bundle
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## Contributing

Contributions are welcome. If you find a bug, have a feature idea, or want to improve the documentation — please open an issue or pull request on GitHub.

1. Fork the repository
2. Create a feature branch: `git checkout -b feature/my-feature`
3. Make your changes and run `composer review` to ensure everything passes
4. Push and open a pull request with a clear description of what changed and why

---

## License

LaraVibe-Vue is open-sourced software licensed under the [MIT license](LICENSE).
