# Action Guide

## Purpose

Actions encapsulate single-purpose business operations so controllers stay thin and expressive.

## Core Principles

- Keep actions focused on one operation.
- Accept typed inputs and return explicit output types.
- Prefer `final readonly class` for generated actions to keep dependencies immutable.
- Avoid side effects that are unrelated to the action responsibility.
- Prefer composition: call other actions/services when needed.

## Generate an Action

```bash
php artisan make:action Account/UpdateProfileAction
```

This generates the class inside app/Actions using stubs/action.stub.

## Example

```php
<?php

declare(strict_types=1);

namespace App\Actions\Account;

use App\Models\User;

final readonly class UpdateProfileAction
{
    public function handle(User $user, string $name, ?string $bio = null): User
    {
        $user->fill([
            'name' => $name,
            'bio' => $bio,
        ])->save();

        return $user->refresh();
    }
}
```

## Best Practices

- Keep validation in Form Requests.
- Use transactions when mutating multiple related records.
- Keep database queries clear and optimized.
- Add focused tests around critical action behavior.
