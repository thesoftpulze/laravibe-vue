# Support Conventions for AI Agents

## Scope

Use this guide when creating or modifying files under `app/Support`.

## Responsibilities

- Keep `app/Support` for cross-cutting framework support only.
- Do not move business/domain logic into this layer.
- Keep helpers small and composable.

## Date Rules

- Keep `App\\Support\\CarbonImmutable` as the default Date class via `Date::use()`.
- Use explicit formatter methods by intent:
    - Transport/API values: `toApiDate()`, `toApiTime()`, `toApiDatetime()`.
    - Human display values: `toStringDate()`, `toStringTime()`, `toStringDatetime()`.
- In resources, serialize model timestamps with the laravibe-standards helpers (`timestamps()`, `createdAt()`, `updatedAt()`, `deletedAt()`). Format other dates in an `attribute()` resolver with the formatters above instead of ad-hoc format strings.

## LaraTweaks Rules

- Register framework tweaks in `App\\Support\\LaraTweaks` and call from `AppServiceProvider::boot()`.
- Keep tweak methods side-effect focused and narrowly scoped.
- Preserve strict model mode, unguarded models, and resource no-wrapping unless explicitly requested to change.
- Models are unguarded globally: do not add `$fillable` or `$guarded` to models, and use `fill()` instead of `forceFill()`.
- Never pass raw request input (`$request->all()`, `$request->input()`) to `create()`, `fill()`, or `update()`; pass `validated()` data or an explicitly built array.

## Helper Function Rules

- Add helpers only when reused in multiple places.
- Use strict return types and clear names.
- Throw framework exceptions for invalid auth/context states instead of returning ambiguous null values.

## Editing Rules

- Follow existing file structure and naming.
- Preserve immutable date behavior.
- Update related tests whenever Support behavior changes.
