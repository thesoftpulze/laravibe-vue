<?php

declare(strict_types=1);

namespace App\Settings;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Override;

final class SettingsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    #[Override]
    public function register(): void
    {
        $this->app->scoped(SettingsRepository::class);

        foreach ($this->groups() as $group) {
            $this->app->scoped($group, fn (Application $app): Settings => $app->make(SettingsRepository::class)->load($group));
        }
    }

    /**
     * Apply the config values the groups override.
     *
     * Caching config skips them, so the cached file keeps the values from `.env` and every request applies the current settings.
     */
    public function boot(SettingsRepository $repository): void
    {
        if ($this->app->runningConsoleCommand('config:cache', 'optimize')) {
            return;
        }

        foreach ($this->groups() as $group) {
            $repository->applyConfig($repository->load($group));
        }
    }

    /**
     * @return list<class-string<Settings>>
     */
    private function groups(): array
    {
        /** @var list<class-string<Settings>> */
        return config()->array('settings.groups');
    }
}
