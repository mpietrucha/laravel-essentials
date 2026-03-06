<?php

namespace Mpietrucha\Laravel\Essentials;

use Illuminate\Contracts\Foundation\Application;
use Mpietrucha\Laravel\Essentials\Auth\CachedEloquentUserProvider;
use Mpietrucha\Laravel\Essentials\Commands\GenerateIdeHelpers;
use Mpietrucha\Laravel\Essentials\Commands\GenerateMixinAnalyzers;
use Mpietrucha\Laravel\Essentials\Commands\Lint;
use Mpietrucha\Laravel\Essentials\Package\Builder;
use Mpietrucha\Laravel\Essentials\Package\ServiceProvider;
use Mpietrucha\Utility\Arr;

class EssentialsServiceProvider extends ServiceProvider
{
    public function configure(Builder $package): void
    {
        $package->name('laravel-essentials');

        $package->hasConsoleCommands([
            Lint::class,
            GenerateIdeHelpers::class,
            GenerateMixinAnalyzers::class,
        ]);
    }

    public function bootingPackage(): void
    {
        auth()->provider('cached', function (Application $application, array $config) {
            $hasher = $application->get('hash');

            $model = Arr::get($config, 'model');

            return new CachedEloquentUserProvider($hasher, $model);
        });
    }
}
