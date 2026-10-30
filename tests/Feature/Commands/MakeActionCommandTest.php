<?php

declare(strict_types=1);

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

use function Pest\Laravel\artisan;

beforeEach(fn () => $this->filesystem = app(Filesystem::class));

it('generates an action class in app/Actions with correct namespace and class name', function (): void {
    $class = 'TmpGenerate' . Str::random(8);
    $path = app_path('Actions/' . $class . '.php');

    try {
        artisan('make:action', ['name' => $class])
            ->expectsOutputToContain('created')
            ->assertSuccessful();

        expect($this->filesystem->exists($path))->toBeTrue()
            ->and(rescue(fn () => $this->filesystem->get($path), fn () => ''))
            ->toContain('declare(strict_types=1);')
            ->toContain('final readonly class ' . $class)
            ->toContain('namespace App\\Actions;')
            ->toContain('public function handle(): void');
    } finally {
        removeAction($this->filesystem, $path);
    }
});

it('does not overwrite an existing action without --force', function (): void {
    $class = 'TmpNoForce' . Str::random(8);
    $path = app_path('Actions/' . $class . '.php');

    $this->filesystem->ensureDirectoryExists(app_path('Actions'));
    $this->filesystem->put($path, "<?php\n\nnamespace App\\Actions;\n\nclass $class {}\n");

    try {
        artisan('make:action', ['name' => $class])
            ->expectsOutputToContain('already exists')
            ->assertSuccessful();

        expect(rescue(fn () => $this->filesystem->get($path), fn () => ''))
            ->toContain("class $class {}");
    } finally {
        removeAction($this->filesystem, $path);
    }
});

it('can overwrite an existing action with --force', function (): void {
    $class = 'TmpForce' . Str::random(8);
    $path = app_path('Actions/' . $class . '.php');

    $this->filesystem->ensureDirectoryExists(app_path('Actions'));
    $this->filesystem->put($path, "<?php\n\nnamespace App\\Actions;\n\nclass $class { public function old() {} }\n");

    try {
        artisan('make:action', ['name' => $class, '--force' => true])
            ->assertSuccessful();

        expect(rescue(fn () => $this->filesystem->get($path), fn () => ''))
            ->toContain('declare(strict_types=1);')
            ->toContain('namespace App\\Actions;')
            ->toContain('final readonly class ' . $class)
            ->toContain('public function handle(): void')
            ->not->toContain('old()');
    } finally {
        removeAction($this->filesystem, $path);
    }
});

function removeAction(Filesystem $filesystem, string $path): void
{
    if ($filesystem->exists($path)) {
        $filesystem->delete($path);
    }
}
