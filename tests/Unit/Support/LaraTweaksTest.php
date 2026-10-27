<?php

declare(strict_types=1);

use App\Support\CarbonImmutable;
use App\Support\LaraTweaks;
use Illuminate\Support\Facades\Date;

test('ensures Date now use App\Support\CarbonImmutable class', function () {
    LaraTweaks::date();

    $date = Date::parse('2025-01-01 12:00 PM');
    expect($date)->toBeInstanceOf(CarbonImmutable::class)
        ->toApiDate()->toBe('2025-01-01')
        ->toApiTime()->toBe('12:00:00')
        ->toApiDatetime()->toBe('2025-01-01T12:00:00+00:00')
        ->toStringDate()->toBe('Jan 1, 2025')
        ->toStringTime()->toBe('12:00 PM')
        ->toStringDatetime()->toBe('Jan 1, 2025 12:00 PM');
});
