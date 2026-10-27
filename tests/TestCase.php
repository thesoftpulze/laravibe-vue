<?php

declare(strict_types=1);

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Http;
use Override;

abstract class TestCase extends BaseTestCase
{
    /**
     * Fail any outbound HTTP request that a test hasn't faked.
     */
    #[Override]
    protected function setUp(): void
    {
        parent::setUp();

        Http::preventStrayRequests();
    }
}
