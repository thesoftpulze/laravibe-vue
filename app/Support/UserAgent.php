<?php

declare(strict_types=1);

namespace App\Support;

/**
 * Minimal user agent parser for display purposes, such as listing browser sessions.
 */
final readonly class UserAgent
{
    /**
     * Platform names keyed by the pattern that identifies them, checked in order.
     *
     * @var array<string, string>
     */
    private const array PLATFORMS = [
        '/iPhone|iPad|iPod/i' => 'iOS',
        '/Android/i' => 'Android',
        '/Windows/i' => 'Windows',
        '/Macintosh|Mac OS X/i' => 'macOS',
        '/CrOS/i' => 'ChromeOS',
        '/Linux/i' => 'Linux',
    ];

    /**
     * Browser names keyed by the pattern that identifies them, checked in order.
     *
     * @var array<string, string>
     */
    private const array BROWSERS = [
        '/Edg(e|A|iOS)?\//i' => 'Edge',
        '/OPR\/|Opera/i' => 'Opera',
        '/Firefox|FxiOS/i' => 'Firefox',
        '/Chrome|CriOS/i' => 'Chrome',
        '/Safari/i' => 'Safari',
    ];

    public function __construct(private string $userAgent) {}

    public function platform(): string
    {
        return $this->match(self::PLATFORMS);
    }

    public function browser(): string
    {
        return $this->match(self::BROWSERS);
    }

    public function isDesktop(): bool
    {
        return preg_match('/Mobile|Android|iPhone|iPad|iPod/i', $this->userAgent) !== 1;
    }

    /**
     * @param  array<string, string>  $patterns
     */
    private function match(array $patterns): string
    {
        foreach ($patterns as $pattern => $name) {
            if (preg_match($pattern, $this->userAgent) === 1) {
                return $name;
            }
        }

        return 'Unknown';
    }
}
