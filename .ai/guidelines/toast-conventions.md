# Toast Conventions for AI Agents

## Scope Rules

- Keep all toast-related backend code in app/Toast.
- Keep toast enums in app/Enums.
- Keep frontend toast contract and rendering in resources/components/toast.

## Usage Rules

- Prefer helper functions for common toast creation:
    - toastSuccess
    - toastError
    - toastWarning
    - toastInfo
- Chain the returned `PendingToast` for extra details: `toastSuccess('Saved')->description('...')->persistent()`. It queues itself when it goes out of scope, or on `send()`.
- Use toastActionCopy, toastActionVisit (same-tab Inertia visit) and toastActionRedirect (new tab) for action payloads.
- In Vue, show client-side toasts with `useToast()` (`success`, `error`, `warning`, `info`, `promise`, `dismiss`).
- Avoid ad-hoc writes to the toasts key, including direct `Inertia::flash('toasts', ...)` calls.

## DTO Rules

- Use App\Toast\DTOs\ToastPayload and App\Toast\DTOs\ToastActionPayload.
- Keep ToastPayload and ToastActionPayload strict and typed.
- Preserve strict unknown-key behavior for DTO hydration.
- Do not add business logic or database access inside toast DTOs.

## Contract Rules

- Allowed toast types must come from App\Enums\ToastType.
- Allowed action types must come from App\Enums\ToastActionType.
- Keep payload keys stable:
    - ToastPayload: type, message, description?, actions?, duration?, dismissible?
    - ToastActionPayload: type, payload, label?
- Any contract change requires updating frontend types and tests in the same change.

## Backend Service Rules

- Continue using App\Toast\Toast as the only transport layer. It queues toasts under the `toasts` key of Inertia flash data.
- Do not reintroduce a `toasts` shared prop; flash data is delivered once and kept out of browser history.
- Assert toast output in tests with `assertInertiaFlash('toasts', [...])`.
- Preserve queue safeguards such as duplicate prevention and queue capping.
- Do not bypass append and pull behavior.

## Frontend Rules

- Keep runtime validation in resources/components/toast/useToast.ts.
- Read toasts from Inertia's `flash` router event, which also fires for the initial page load.
- Mount the single Toaster from `registerToaster()`, outside the page tree. Do not put `ToastProvider` in layouts; a layout change would drop toasts.
- Keep placement and per-type durations in `ToastProvider.vue` and `useToast.ts`. A `duration` of 0 keeps the toast open until it is closed.
- Keep toaster registration idempotent and cleaned up on HMR disposal.
- Ensure Toast.vue supports all action types and remains type-safe.

## Testing Rules

- Update tests when changing toast behavior:
    - tests/Feature/Toast/ToastTest.php
    - tests/Feature/Toast/HelperTest.php
- Add at least one integration-level assertion when controller behavior changes toast output.
- Run focused tests first:
    - php artisan test --compact tests/Feature/Toast/ToastTest.php tests/Feature/Toast/HelperTest.php
