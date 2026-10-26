<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Laravel\Wayfinder\GenerateCommand;
use Override;

/**
 * Generates Wayfinder files into `resources/` by default instead of the package's `resources/js`,
 * so the output always matches the `@/actions` and `@/routes` import aliases.
 */
final class WayfinderGenerateCommand extends GenerateCommand
{
    #[Override]
    public function handle(): void
    {
        $this->input->setOption('path', $this->option('path') ?? resource_path());

        parent::handle();
    }
}
