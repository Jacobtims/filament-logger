<?php

namespace Jacobtims\FilamentLogger;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Jacobtims\FilamentLogger\Resources\ActivityResource;

class FilamentLoggerPlugin implements Plugin
{
    public function getId(): string
    {
        return 'filament-logger';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([
            ActivityResource::class,
        ]);
    }

    public function boot(Panel $panel): void
    {
        //
    }

    public static function make(): static
    {
        return app(static::class);
    }

    public static function get(): static
    {
        /** @var static $plugin */
        $plugin = filament(app(static::class)->getId());

        return $plugin;
    }
}
