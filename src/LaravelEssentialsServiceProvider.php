<?php

namespace Mpietrucha\Laravel\Essentials;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Hashing\HashManager;
use Illuminate\Support\Arr;
use Mpietrucha\Laravel\Essentials\Auth\CachedEloquentUserProvider;
use Mpietrucha\Laravel\Essentials\Commands\GenerateIdeHelpers;
use Mpietrucha\Laravel\Essentials\Commands\GenerateMixinAnalyzers;
use Mpietrucha\Laravel\Essentials\Commands\Lint;
use Mpietrucha\Laravel\Essentials\Commands\SyncTranslations;
use Mpietrucha\Laravel\Essentials\Mixins\BlueprintMixin;
use Mpietrucha\Laravel\Essentials\Mixins\EloquentBuilderMixin;
use Mpietrucha\Laravel\Essentials\PackageTools\Package;
use Mpietrucha\Laravel\Essentials\PackageTools\PackageServiceProvider;
use Mpietrucha\Laravel\Essentials\Translation\SpatieTranslationLoaderManager;

class LaravelEssentialsServiceProvider extends PackageServiceProvider
{
    public function configure(Package $package): void
    {
        $package->name('laravel-essentials');

        $package->hasConfigFile('app');

        $package->hasMixins([
            Blueprint::class => BlueprintMixin::class,
            Builder::class => EloquentBuilderMixin::class,
        ]);

        $package->hasConsoleCommands([
            Lint::class,
            SyncTranslations::class,
            GenerateIdeHelpers::class,
            GenerateMixinAnalyzers::class,
        ]);
    }

    public function bootingPackage(): void
    {
        auth()->provider('cached', static function (Application $application, array $config): CachedEloquentUserProvider {
            $hashManager = $application->get(HashManager::class);

            $model = Arr::string($config, 'model');

            return new CachedEloquentUserProvider($hashManager, $model);
        });
    }

    public function registeringPackage(): void
    {
        config([
            'translation-loader.translation_manager' => SpatieTranslationLoaderManager::class,
        ]);
    }
}
