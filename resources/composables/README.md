# Composables

Vue composables that wrap Inertia shared props into typed, ergonomic APIs.

---

## `useAuth`

**File:** `resources/composables/useAuth.ts`

Provides typed access to the authenticated user and UI ability checks. Backed entirely by Inertia shared props — no extra HTTP calls.

### API

| Export            | Type                           | Description                                                                                  |
| ----------------- | ------------------------------ | -------------------------------------------------------------------------------------------- |
| `user`            | `ComputedRef<User \| null>`    | Nullable user. Use on public pages.                                                          |
| `isAuthenticated` | `ComputedRef<boolean>`         | `true` when a user session is active.                                                        |
| `can(ability)`    | `(ability: string) => boolean` | Returns the ability value from `auth.can`. Defaults to `false` for unknown keys.             |
| `requireUser()`   | `() => User`                   | Returns the user or **throws**. Use only on pages that are always behind an auth middleware. |

### Usage

#### Public page (user may or may not be logged in)

```vue
<script setup lang="ts">
import { useAuth } from '@/composables/useAuth'

const { user, isAuthenticated } = useAuth()
</script>

<template>
    <nav>
        <Link v-if="isAuthenticated" href="/account">Account</Link>
        <Link v-else href="/login">Log in</Link>
    </nav>
</template>
```

#### Protected page (always behind `auth` middleware)

Use `requireUser()` to get a non-null `User` directly — no `?.` optional chaining needed.

```vue
<script setup lang="ts">
import { useAuth } from '@/composables/useAuth'

const { requireUser } = useAuth()
const user = requireUser() // User (not User | null)
</script>

<template>
    <p>Welcome, {{ user.name }}</p>
    <p>{{ user.email }}</p>
</template>
```

#### Ability / permission checks

```vue
<script setup lang="ts">
import { useAuth } from '@/composables/useAuth'

const { can } = useAuth()
</script>

<template>
    <Button v-if="can('updateProfile')">Save Changes</Button>
    <Button v-if="can('deleteAccount')" variant="destructive">Delete Account</Button>
</template>
```

### Shared props contract

The composable reads from `page.props.auth`, which is always present in every Inertia response. The shape is defined in `resources/types/index.d.ts`:

```ts
interface Auth {
    user: User | null // null for guests, User object when authenticated
    can: Record<string, boolean> // UI ability map resolved server-side
}
```

### Adding abilities

Ability keys are resolved server-side in `HandleInertiaRequests::resolveAbilities()` from gates defined in `AppServiceProvider::configureGates()`. Define a gate, add its key to both branches, and it becomes available via `can()` on the frontend automatically:

```php
// app/Providers/AppServiceProvider.php
Gate::define('manageTeam', fn (User $user): bool => $user->hasTeam());

// app/Http/Middleware/HandleInertiaRequests.php
private function resolveAbilities(?User $user): array
{
    if (! $user instanceof User) {
        return [
            'updateProfile' => false,
            'deleteAccount' => false,
            'manageTeam' => false, // ← new ability
        ];
    }

    $gate = Gate::forUser($user);

    return [
        'updateProfile' => $gate->allows('updateProfile'),
        'deleteAccount' => $gate->allows('deleteAccount'),
        'manageTeam' => $gate->allows('manageTeam'), // ← new ability
    ];
}
```

Authorize the matching request with the same gate (for example `Gate::allows('manageTeam')` in a Form Request's `authorize()`), so the UI and the server share one rule.

### Security notes

- Abilities are resolved using server-side Laravel gates/policies — the frontend only receives **boolean** values, never raw policy logic.
- The shared `User` payload contains only safe fields: `id`, `name`, `email`, and optionally `email_verified_at`. Sensitive fields (`password`, `remember_token`, timestamps) are never serialized.
- `requireUser()` throws a JavaScript error. This is intentional — reaching a protected page without auth indicates a routing misconfiguration, not a user-facing scenario.

### User shape

Defined in `resources/types/models.d.ts` and mirrors the `UserResource` backend payload:

```ts
interface User {
    id: number
    name: string
    email: string
    email_verified_at?: string | null
}
```

---

## `usePageMeta`

**File:** `resources/composables/usePageMeta.ts`

Returns the current page's `PageMeta` props (title, breadcrumbs, etc.) as a computed ref. See [app/DTOs/PageMeta.php](../../app/DTOs/PageMeta.php) for the full `PageMeta` DTO shape.

```ts
const meta = usePageMeta()
// meta.value.title
```

---

## `useAppearance`

**File:** `resources/composables/useAppearance.ts`

Manages the active colour theme (`light`, `dark`, or `system`) and persists the preference via a cookie.

---

## `useInitials`

**File:** `resources/composables/useInitials.ts`

Utility composable that returns a function for generating initials from a full name string.
