<?php

declare(strict_types=1);

use App\Enums\SortDirection;
use App\Settings\Attributes\Encrypted;
use App\Settings\GeneralSettings;
use App\Settings\Settings;
use App\Settings\SettingsRepository;
use App\Settings\SettingsServiceProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

final class ExampleSettings extends Settings
{
    public string $title = 'Example';

    public ?string $subtitle = null;

    public int $perPage = 15;

    public float $ratio = 1.5;

    public bool $enabled = false;

    /** @var list<string> */
    public array $tags = [];

    public SortDirection $direction = SortDirection::ASC;

    #[Encrypted]
    public ?string $apiKey = null;

    public static function group(): string
    {
        return 'example';
    }
}

final class SettingsWithoutDefault extends Settings
{
    public string $title;

    public static function group(): string
    {
        return 'without-default';
    }
}

/**
 * Store a raw payload, as an older version of the application could have saved it.
 */
function storeRawSetting(string $group, string $name, mixed $payload): void
{
    DB::table('settings')->insert(['group' => $group, 'name' => $name, 'payload' => json_encode($payload)]);
    app(SettingsRepository::class)->forget();
}

test('a group has its defaults until values are saved', function () {
    expect(app(SettingsRepository::class)->load(ExampleSettings::class)->toArray())->toBe([
        'title' => 'Example',
        'subtitle' => null,
        'perPage' => 15,
        'ratio' => 1.5,
        'enabled' => false,
        'tags' => [],
        'direction' => SortDirection::ASC,
        'apiKey' => null,
    ]);
});

test('saved values of every supported type load back', function () {
    $repository = app(SettingsRepository::class);

    $repository->save(new ExampleSettings()->fill([
        'title' => 'Changed',
        'subtitle' => 'A subtitle',
        'perPage' => 50,
        'ratio' => 2.0,
        'enabled' => true,
        'tags' => ['one', 'two'],
        'direction' => SortDirection::DESC,
        'apiKey' => 'secret-key',
    ]));
    $repository->forget();

    expect($repository->load(ExampleSettings::class)->toArray())->toBe([
        'title' => 'Changed',
        'subtitle' => 'A subtitle',
        'perPage' => 50,
        'ratio' => 2.0,
        'enabled' => true,
        'tags' => ['one', 'two'],
        'direction' => SortDirection::DESC,
        'apiKey' => 'secret-key',
    ]);
});

test('only values that differ from their defaults are stored', function () {
    $repository = app(SettingsRepository::class);

    $repository->save(new ExampleSettings()->fill(['title' => 'Changed', 'perPage' => 50]));

    expect(DB::table('settings')->where('group', 'example')->pluck('name')->sort()->values()->all())->toBe(['perPage', 'title']);

    $repository->save(new ExampleSettings()->fill(['perPage' => 50]));

    expect(DB::table('settings')->where('group', 'example')->pluck('name')->all())->toBe(['perPage']);
});

test('encrypted settings are not stored in plain text', function () {
    app(SettingsRepository::class)->save(new ExampleSettings()->fill(['apiKey' => 'secret-key']));

    expect(DB::table('settings')->where('name', 'apiKey')->value('payload'))->not->toContain('secret-key');
});

test('a stored value that no longer fits its setting keeps the default', function (string $name, mixed $payload) {
    storeRawSetting('example', $name, $payload);

    $settings = app(SettingsRepository::class)->load(ExampleSettings::class);

    expect($settings->toArray()[$name])->toBe(new ExampleSettings()->toArray()[$name]);
})->with([
    'string as int' => ['title', 42],
    'null for a non-nullable setting' => ['perPage', null],
    'int as string' => ['perPage', '50'],
    'unknown enum value' => ['direction', 'sideways'],
    'enum as array' => ['direction', ['asc']],
    'unencrypted secret' => ['apiKey', 'not-encrypted'],
]);

test('every group is read with one cached query', function () {
    app(SettingsRepository::class)->save(new ExampleSettings()->fill(['title' => 'Changed']));

    DB::enableQueryLog();

    app(SettingsRepository::class)->load(ExampleSettings::class);
    app(SettingsRepository::class)->load(GeneralSettings::class);
    app()->forgetScopedInstances();
    $settings = app(SettingsRepository::class)->load(ExampleSettings::class);

    expect(DB::getQueryLog())->toHaveCount(1)
        ->and($settings->title)->toBe('Changed');
});

test('saving a group makes its new values visible straight away', function () {
    app(SettingsRepository::class)->load(ExampleSettings::class);

    app(SettingsRepository::class)->save(new ExampleSettings()->fill(['title' => 'Changed']));

    expect(app(SettingsRepository::class)->load(ExampleSettings::class)->title)->toBe('Changed');
});

test('groups keep their defaults while the settings table is missing', function () {
    Schema::drop('settings');
    app(SettingsRepository::class)->forget();

    expect(app(SettingsRepository::class)->load(ExampleSettings::class)->title)->toBe('Example');
});

test('filling an unknown setting is rejected', function () {
    new ExampleSettings()->fill(['unknown' => 'value']);
})->throws(InvalidArgumentException::class, '[ExampleSettings] has no setting named [unknown].');

test('every setting needs a default', function () {
    SettingsWithoutDefault::fromPayload([]);
})->throws(LogicException::class, 'Setting [SettingsWithoutDefault::$title] needs a default value.');

test('registered groups are resolved from the container with their saved values', function () {
    app(SettingsRepository::class)->save(new GeneralSettings()->fill(['tagline' => 'Build faster']));

    expect(app(GeneralSettings::class)->tagline)->toBe('Build faster');
});

test('the site name setting overrides the app name and falls back to it when cleared', function () {
    $appName = config('app.name');
    $repository = app(SettingsRepository::class);

    $repository->save(new GeneralSettings()->fill(['siteName' => 'Acme']));

    expect(config('app.name'))->toBe('Acme');
    $this->get(route('home'))->assertInertia(fn ($page) => $page->where('name', 'Acme'));

    $repository->save(new GeneralSettings()->fill(['siteName' => null]));

    expect(config('app.name'))->toBe($appName);
});

test('stored settings override config when the application boots', function () {
    storeRawSetting('general', 'siteName', 'Acme');
    app()->forgetScopedInstances();

    new SettingsServiceProvider(app())->boot(app(SettingsRepository::class));

    expect(config('app.name'))->toBe('Acme');
});
