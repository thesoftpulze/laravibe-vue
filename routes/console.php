<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schedule;

Schedule::command('auth:clear-resets')->everyFifteenMinutes();
Schedule::command('queue:prune-batches')->daily();
Schedule::command('queue:prune-failed')->daily();
