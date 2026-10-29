<?php

declare(strict_types=1);

namespace App\Settings;

use Illuminate\Contracts\Cache\Repository as CacheRepository;
use Illuminate\Contracts\Config\Repository as ConfigRepository;
use Illuminate\Contracts\Container\Container;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\QueryException;

/**
 * Loads and saves settings groups. Every stored value is read with one query and cached until a group is saved.
 */
final class SettingsRepository
{
    public const string CACHE_KEY = 'settings';

    private const string TABLE = 'settings';

    /**
     * @var array<string, array<string, mixed>>|null
     */
    private ?array $stored = null;

    /**
     * Config values as they were before a settings group overrode them.
     *
     * @var array<string, mixed>
     */
    private array $originalConfig = [];

    public function __construct(
        private readonly ConnectionInterface $database,
        private readonly CacheRepository $cache,
        private readonly ConfigRepository $config,
        private readonly Container $container,
    ) {}

    /**
     * Load a settings group, with its defaults in place of values that are not stored.
     *
     * @template TSettings of Settings
     *
     * @param  class-string<TSettings>  $class
     * @return TSettings
     */
    public function load(string $class): Settings
    {
        return $class::fromPayload($this->stored()[$class::group()] ?? []);
    }

    /**
     * Store a settings group and apply it to this request straight away.
     *
     * Only values that differ from their defaults are stored, so a changed default reaches every value nobody has set.
     */
    public function save(Settings $settings): void
    {
        $group = $settings::group();
        $payload = $settings->toPayload();
        $now = now();

        $rows = array_map(fn (string $name): array => [
            'group' => $group,
            'name' => $name,
            'payload' => json_encode($payload[$name], JSON_THROW_ON_ERROR),
            'created_at' => $now,
            'updated_at' => $now,
        ], array_keys($payload));

        $this->database->transaction(function () use ($group, $payload, $rows): void {
            $this->database->table(self::TABLE)
                ->where('group', $group)
                ->whereNotIn('name', array_keys($payload))
                ->delete();

            if ($rows !== []) {
                $this->database->table(self::TABLE)->upsert($rows, ['group', 'name'], ['payload', 'updated_at']);
            }
        });

        $this->forget();
        $this->container->instance($settings::class, $settings);
        $this->applyConfig($settings);
    }

    /**
     * Override config with a group's values. A null value restores the config value the group replaced.
     */
    public function applyConfig(Settings $settings): void
    {
        if (! $settings instanceof OverridesConfig) {
            return;
        }

        foreach ($settings->config() as $key => $value) {
            if (! array_key_exists($key, $this->originalConfig)) {
                $this->originalConfig[$key] = $this->config->get($key);
            }

            $this->config->set($key, $value ?? $this->originalConfig[$key]);
        }
    }

    /**
     * Forget the stored values, so the next load reads them from the database.
     */
    public function forget(): void
    {
        $this->stored = null;
        $this->cache->forget(self::CACHE_KEY);
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    private function stored(): array
    {
        if ($this->stored !== null) {
            return $this->stored;
        }

        try {
            /** @var array<string, array<string, mixed>> $stored */
            $stored = $this->cache->rememberForever(self::CACHE_KEY, $this->query(...));
        } catch (QueryException) {
            // The settings or cache table does not exist yet, for example before the first migration, so every group keeps its defaults.
            return [];
        }

        return $this->stored = $stored;
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    private function query(): array
    {
        $stored = [];

        foreach ($this->database->table(self::TABLE)->get(['group', 'name', 'payload']) as $row) {
            /** @var object{group: string, name: string, payload: string} $row */
            $stored[$row->group][$row->name] = json_decode($row->payload, true, flags: JSON_THROW_ON_ERROR);
        }

        return $stored;
    }
}
