# Support Conventions for AI Agents

## Scope

Use this guide when creating or modifying files under `app/Support`, or when using the helpers it provides.

## Responsibilities

- Keep `app/Support` for cross-cutting framework support only.
- Do not move business/domain logic into this layer.
- Keep classes small, `final`, and free of database access.

## Helper Usage Rules

- Render Inertia pages with `vue($component, $props, $pageMeta)`, not `Inertia::render()` or `inertia()`.
- Wrap Inertia props with `optionalProp()`, `deferProp()`, and `alwaysProp()` instead of calling the `Inertia` facade.
- Use `authUser()` wherever the user must be authenticated, instead of `$request->user()` or `auth()->user()` with null checks.
- Use `App\\Support\\UserAgent` to show the platform and browser of a user agent. Do not add a user agent parsing package.

## Date Rules

- Keep `App\\Support\\CarbonImmutable` as the default Date class via `Date::use()`.
- Use explicit formatter methods by intent:
    - Transport/API values: `toApiDate()`, `toApiTime()`, `toApiDatetime()`.
    - Human display values: `toStringDate()`, `toStringTime()`, `toStringDatetime()`.
- Display formatters use the app timezone (UTC). Send `toApiDatetime()` and format in the browser when a date-time shown to users depends on their timezone.
- In resources, serialize model timestamps with the laravibe-standards helpers (`timestamps()`, `createdAt()`, `updatedAt()`, `deletedAt()`). Format other dates in an `attribute()` resolver with the formatters above instead of ad-hoc format strings.

## LaraTweaks Rules

- Register framework tweaks in `App\\Support\\LaraTweaks` and call from `AppServiceProvider::boot()`.
- Keep tweak methods side-effect focused and narrowly scoped.
- Preserve unguarded models, strict model mode outside production, and resource no-wrapping unless explicitly requested to change.
- Models are unguarded globally: do not add `$fillable` or `$guarded` to models, and use `fill()` instead of `forceFill()`.
- Never pass raw request input (`$request->all()`, `$request->input()`) to `create()`, `fill()`, or `update()`; pass `validated()` data or an explicitly built array.
- Put test-only safeguards in `Tests\\TestCase` or `phpunit.xml`, not in `LaraTweaks`.

## Helper Function Rules

- Add a global helper only for a framework call the app makes repeatedly, and document it in `app/Support/README.md`.
- Use strict return types, PHPDoc, and clear names.
- Throw framework exceptions for invalid auth/context states instead of returning ambiguous null values.

## Editing Rules

- Follow existing file structure and naming.
- Preserve immutable date behavior.
- Update `tests/Feature/Support` and `tests/Unit/Support` whenever Support behavior changes.
