<?php

declare(strict_types=1);

namespace App\Support;

use Carbon\CarbonImmutable as BaseCarbonImmutable;

final class CarbonImmutable extends BaseCarbonImmutable
{
    public function toApiDate(): string
    {
        return $this->format('Y-m-d');
    }

    public function toApiTime(): string
    {
        return $this->format('H:i:s');
    }

    public function toApiDatetime(): string
    {
        return $this->format(DATE_ATOM);
    }

    public function toStringDate(): string
    {
        return $this->format('M j, Y');
    }

    public function toStringTime(): string
    {
        return $this->format('h:i A');
    }

    public function toStringDatetime(): string
    {
        return $this->format('M j, Y h:i A');
    }
}
