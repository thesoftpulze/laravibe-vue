<?php

declare(strict_types=1);

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

use function Pest\Laravel\artisan;

beforeEach(fn () => $this->filesystem = app(Filesystem::class));

it('generates an dto class in app/DTOs with correct namespace and class name', function (): void {
    $class = 'TmpGenerate' . Str::random(8);
    $path = app_path('DTOs/' . $class . '.php');

    try {
        artisan('make:dto', ['name' => $class])
            ->expectsOutputToContain('created')
            ->assertSuccessful();

        expect($this->filesystem->exists($path))->toBeTrue()
            ->and(rescue(fn () => $this->filesystem->get($path), fn () => ''))
            ->toContain('declare(strict_types=1);')
            ->and(rescue(fn () => $this->filesystem->get($path), fn () => ''))
            ->toContain('namespace App\\DTOs;')
            ->toContain('use TheSoftPulze\\LaravibeStandards\\DTOs\\Concerns\\AsDTO;')
            ->toContain('final readonly class ' . $class)
            ->toContain('use AsDTO;')
            ->toContain('public function __construct(')
            ->toContain('public string $name')
            ->toContain('public ?string $description = null');
    } finally {
        removeDTO($this->filesystem, $path);
    }

});

it('does not overwrite an existing dto without --force', function (): void {
    $class = 'TmpNoForce' . Str::random(8);
    $path = app_path('DTOs/' . $class . '.php');

    // Seed an existing file
    $this->filesystem->ensureDirectoryExists(app_path('DTOs'));
    $this->filesystem->put($path, "<?php\n\nnamespace App\\DTOs;\n\nclass $class {}\n");

    try {

        artisan('make:dto', ['name' => $class])
            ->expectsOutputToContain('already exists')
            ->assertSuccessful();

        expect(rescue(fn () => $this->filesystem->get($path), fn () => ''))
            ->toContain("class $class {}");
    } finally {
        removeDTO($this->filesystem, $path);
    }

});

it('can overwrite an existing dto with --force', function (): void {
    $class = 'TmpForce' . Str::random(8);
    $path = app_path('DTOs/' . $class . '.php');

    // Seed an existing file
    $this->filesystem->ensureDirectoryExists(app_path('DTOs'));
    $this->filesystem->put($path, "<?php\n\nnamespace App\\DTOs;\n\nclass $class { public function old() {} }\n");

    try {
        artisan('make:dto', ['name' => $class, '--force' => true])
            ->assertSuccessful();

        expect(rescue(fn () => $this->filesystem->get($path), fn () => ''))
            ->toContain('namespace App\\DTOs;')
            ->toContain('final readonly class ' . $class)
            ->toContain('public function __construct(')
            ->toContain('use TheSoftPulze\\LaravibeStandards\\DTOs\\Concerns\\AsDTO;')
            ->toContain('use AsDTO;')
            ->not->toContain('old()');
    } finally {
        removeDTO($this->filesystem, $path);
    }

});

function removeDTO(Filesystem $filesystem, string $path): void
{
    if ($filesystem->exists($path)) {
        $filesystem->delete($path);
    }
}
