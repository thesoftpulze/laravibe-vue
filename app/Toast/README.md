# Toast System

## Overview

The Toast module provides a typed notification pipeline from Laravel to Vue, delivered as Inertia flash data.

Flow:

1. Create toast payloads in PHP (helpers, DTO factories, or direct DTO construction).
2. Queue them as Inertia flash data through App\Toast\Toast.
3. Inertia delivers the flash data once with the next page and fires the `flash` router event.
4. Render in Vue with vue-sonner.

## PHP Usage

### Recommended helpers

Use the global helper functions for common toasts:

- toastSuccess(string $message, ToastActionPayload ...$actions): PendingToast
- toastError(string $message, ToastActionPayload ...$actions): PendingToast
- toastWarning(string $message, ToastActionPayload ...$actions): PendingToast
- toastInfo(string $message, ToastActionPayload ...$actions): PendingToast

Use action helpers when needed:

- toastActionCopy(string $payloadToCopy, ?string $label = null): ToastActionPayload
- toastActionVisit(string $url, ?string $label = null): ToastActionPayload — same-tab Inertia visit
- toastActionRedirect(string $redirectURL, ?string $label = null): ToastActionPayload — opens a new tab

Example:

```php
toastSuccess(
    'Profile updated successfully.',
    toastActionVisit(route('account'), 'Open Account')
);
```

### Builder

The helpers (and `toast()->success()`, `error()`, `warning()`, `info()`) return a `PendingToast`. Chain it for extra details:

```php
toastSuccess('Invoice sent.')
    ->description('A copy went to billing@example.com.')
    ->action(toastActionVisit(route('invoices.show', $invoice), 'View'))
    ->persistent();      // or ->duration(10_000)
```

Like Laravel's `PendingDispatch`, it queues itself when it goes out of scope. If you keep it in a variable, call `send()` to queue it straight away; it is never queued twice.

### ToastException

Throw `ToastException` to go back with a toast. It accepts a type, description and actions:

```php
throw new ToastException('Payment failed.', description: 'Your card was declined.', actions: [toastActionVisit('/billing', 'Update card')]);
```

### DTO-first usage

If you need full control, append a ToastPayload manually:

```php
use App\Enums\ToastType;
use App\Toast\DTOs\ToastPayload;

$toast = new ToastPayload(
    type: ToastType::SUCCESS,
    message: 'Saved',
    description: 'Your changes are live.',
    actions: null,
    duration: 8000,
    dismissible: true,
);

toast()->append($toast);
```

ToastPayload supports strict hydration via fromArray and rejects unknown keys.

## Payload Contract

ToastPayload serializes to:

- type: error | success | warning | info
- message: string
- description?: string
- actions?: array of ToastActionPayload
- duration?: int (milliseconds, 0 keeps it open until closed)
- dismissible?: bool (whether the user can close it before it times out)

ToastActionPayload serializes to:

- type: copy | redirect | visit
- payload: string
- label?: string | null

## Queue Behavior

The backend Toast service:

- Stores toasts under the `toasts` key of Inertia flash data (`Inertia::flash()`), so they never land in browser history
- Prevents immediate consecutive duplicate entries
- Caps queue length to 5 entries

Assert toasts in feature tests with `assertInertiaFlash('toasts', [...])`.

## Vue Behavior

Frontend handling is in:

- resources/components/toast/useToast.ts
- resources/components/toast/Toast.vue
- resources/components/toast/ToastProvider.vue

Current behavior includes:

- One Toaster, mounted by `registerToaster()` outside the page tree, so toasts survive layout changes
- Placement in ToastProvider.vue: bottom-right on desktop, top-center on phones
- Per-type default durations (success/info 4s, warning 6s, error 8s), overridable per toast
- Runtime guards for incoming toast payloads
- Idempotent toaster registration and cleanup
- Action rendering for copy, visit and redirect types

Client-side toasts use the same renderer:

```ts
const toast = useToast()

toast.success('Copied.', { description: 'Paste it anywhere.' })
const id = toast.info('Uploading…', { duration: 0 })
toast.dismiss(id)

// Loading toast that turns into a success or error toast; rethrows on failure.
await toast.promise(saveDraft(), { loading: 'Saving…', success: 'Draft saved.', error: 'Could not save the draft.' })
```

## Testing

Primary coverage:

- tests/Feature/Toast/ToastTest.php
- tests/Feature/Toast/HelperTest.php

Run:

```bash
php artisan test --compact tests/Feature/Toast/ToastTest.php tests/Feature/Toast/HelperTest.php
```

## Notes

- Keep ToastType and ToastActionType as the source of truth for allowed values.
- Prefer helper functions for common controller usage.
- Use DTO constructors or fromArray only when extra metadata or custom composition is required.
