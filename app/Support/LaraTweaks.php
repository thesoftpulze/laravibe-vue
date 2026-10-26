<?php

declare(strict_types=1);

namespace App\Support;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Vite;
use Illuminate\Validation\Rules\Password;

final class LaraTweaks
{
    public static function default(): void
    {
        self::dbCommands();
        self::passwords();
        self::urls();
        self::date();
        self::models();
        self::resources();
        self::vite();
    }

    /**
     * Unguard models and enable strict mode. Models don't declare `$fillable` or `$guarded`,
     * so only pass validated or explicitly built arrays to `create()`, `fill()` and `update()`.
     */
    public static function models(): void
    {
        Model::unguard();
        Model::shouldBeStrict();
    }

    public static function resources(): void
    {
        JsonResource::withoutWrapping();
    }

    public static function dbCommands(): void
    {
        DB::prohibitDestructiveCommands(app()->isProduction());
    }

    public static function date(): void
    {
        // CarbonImmutable as default
        Date::use(CarbonImmutable::class);
    }

    public static function vite(): void
    {
        Vite::prefetch(concurrency: 3);
    }

    /**
     * Require strong, uncompromised passwords in production only.
     */
    public static function passwords(): void
    {
        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null);
    }

    /**
     * Force HTTPS URLs in production, leaving local HTTP setups untouched.
     */
    public static function urls(): void
    {
        URL::forceHttps(app()->isProduction());
    }
}
