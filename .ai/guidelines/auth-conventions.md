# Auth Conventions for AI Agents

## Fortify Integration Rules

- Render auth pages through the `Fortify::*View()` callbacks in `App\Providers\FortifyServiceProvider`, using `vue()` with a `PageMeta`.
- Share validation rules through `App\Concerns\PasswordValidationRules` and `App\Concerns\ProfileValidationRules`.
- Only enable Fortify features the UI actually supports in `config/fortify.php`.

## Shared Props Rules

- `auth.user` must always be present in every Inertia response — use `null` for guests, never omit the key.
- `auth.can` must always be present as a flat `Record<string, boolean>` ability map.
- The default shared user payload is minimal: `id`, `name`, `email`, and optionally `email_verified_at`.
- Never include `password`, `remember_token`, or internal timestamps in the shared auth payload.
- Ability values are resolved server-side in `HandleInertiaRequests::resolveAbilities()`. Return booleans only.

## Adding Abilities Rules

- Add new ability keys to both the guest (`$user === null`) and authenticated branches of `resolveAbilities()`.
- Resolve ability values using Laravel gates or policy checks — never duplicate authorization logic on the frontend.
- Keep the ability map small and UI-focused. Backend-only authorization must stay backend-only.

## TypeScript Type Rules

- `Auth.user` must be typed as `User | null`, never `User | undefined` or `User?`.
- `Auth.can` must be typed as `Record<string, boolean>`.
- The `User` interface must exactly mirror the fields returned by `UserResource`. Do not add fields that are not serialized by the backend.
- Optional fields on `User` (such as `avatar`, `email_verified_at`) must remain optional with explicit `Nullable<T>` or `string | null` types.

## Composable Usage Rules

- Always import auth state from `useAuth` — never read `usePage().props.auth` directly in a component.
- Use `user` (nullable computed) on public pages where a visitor may or may not be authenticated.
- Use `requireUser()` on pages that are always behind an `auth` middleware. It returns a non-null `User` and eliminates optional-chaining noise.
- Use `can(ability)` for UI gating (show/hide elements). Never use it as a substitute for server-side authorization.
- Do not use `isAuthenticated` as a guard for sensitive operations — always rely on backend middleware.

## Security Rules

- Do not expose sensitive model fields (`password`, `remember_token`, internal tokens) in any shared Inertia prop.
- Ability values must be booleans. Never serialize raw policy objects, role strings, or permission arrays to the frontend.
- Client-side `can()` checks are UI conveniences only. Every sensitive action must be authorized on the server.
