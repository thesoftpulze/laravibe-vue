<?php

declare(strict_types=1);

use App\Support\UserAgent;

it('detects the platform, browser and device type', function (string $userAgent, string $platform, string $browser, bool $isDesktop): void {
    $parsed = new UserAgent($userAgent);

    expect($parsed)
        ->platform()->toBe($platform)
        ->browser()->toBe($browser)
        ->isDesktop()->toBe($isDesktop);
})->with([
    'chrome on macOS' => ['Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/130.0.0.0 Safari/537.36', 'macOS', 'Chrome', true],
    'edge on windows' => ['Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/130.0.0.0 Safari/537.36 Edg/130.0.0.0', 'Windows', 'Edge', true],
    'firefox on linux' => ['Mozilla/5.0 (X11; Linux x86_64; rv:131.0) Gecko/20100101 Firefox/131.0', 'Linux', 'Firefox', true],
    'safari on iPhone' => ['Mozilla/5.0 (iPhone; CPU iPhone OS 18_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/18.0 Mobile/15E148 Safari/604.1', 'iOS', 'Safari', false],
    'chrome on android' => ['Mozilla/5.0 (Linux; Android 14; Pixel 8) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/130.0.0.0 Mobile Safari/537.36', 'Android', 'Chrome', false],
]);

it('falls back to unknown for an empty user agent', function (): void {
    $parsed = new UserAgent('');

    expect($parsed)
        ->platform()->toBe('Unknown')
        ->browser()->toBe('Unknown');
});
