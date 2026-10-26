<?php

declare(strict_types=1);

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

use function Pest\Laravel\artisan;

beforeEach(fn () => $this->filesystem = app(Filesystem::class));

it('generates wayfinder files into resources by default', function (): void {
    $helperPath = resource_path('wayfinder/index.ts');
    $this->filesystem->delete($helperPath);

    artisan('wayfinder:generate')->assertSuccessful();

    expect($this->filesystem->exists($helperPath))->toBeTrue()
        ->and($this->filesystem->exists(resource_path('routes/index.ts')))->toBeTrue()
        ->and($this->filesystem->isDirectory(resource_path('actions/App')))->toBeTrue()
        ->and($this->filesystem->isDirectory(resource_path('js')))->toBeFalse();
});

it('generates wayfinder files into the given --path', function (): void {
    $path = storage_path('framework/testing/wayfinder-' . Str::random(8));

    try {
        artisan('wayfinder:generate', ['--path' => $path])->assertSuccessful();

        expect($this->filesystem->exists($path . '/wayfinder/index.ts'))->toBeTrue()
            ->and($this->filesystem->exists($path . '/routes/index.ts'))->toBeTrue();
    } finally {
        $this->filesystem->deleteDirectory($path);
    }
});
