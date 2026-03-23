<?php

namespace Mpietrucha\Laravel\Essentials;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Support\Arr;
use Mpietrucha\Laravel\Essentials\Auth\CachedEloquentUserProvider;
use Mpietrucha\Laravel\Essentials\Commands\GenerateIdeHelpers;
use Mpietrucha\Laravel\Essentials\Commands\GenerateMixinAnalyzers;
use Mpietrucha\Laravel\Essentials\Commands\Lint;
use Mpietrucha\Laravel\Essentials\Package\Builder;
use Mpietrucha\Laravel\Essentials\Package\ServiceProvider;

class EssentialsServiceProvider extends ServiceProvider
{
    public function configure(Builder $package): void
    {
        $package->name('laravel-essentials');

        $package->hasConfigFile('app');

        $package->hasConsoleCommands([
            Lint::class,
            GenerateIdeHelpers::class,
            GenerateMixinAnalyzers::class,
        ]);
    }

    public function bootingPackage(): void
    {
        auth()->provider('cached', static function (Application $application, array $config): CachedEloquentUserProvider {
            /** @var Hasher $hasher */
            $hasher = $application->get('hash');

            $model = Arr::string($config, 'model');

            return new CachedEloquentUserProvider($hasher, $model);
        });
    }
}
