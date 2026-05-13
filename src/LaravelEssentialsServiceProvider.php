<?php

namespace Mpietrucha\Laravel\Essentials;

use Illuminate\Contracts\Debug\ExceptionHandler;
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
use Mpietrucha\Laravel\Essentials\Eloquent\Models\Discount;
use Mpietrucha\Laravel\Essentials\Mixins\BlueprintMixin;
use Mpietrucha\Laravel\Essentials\Mixins\EloquentBuilderMixin;
use Mpietrucha\Laravel\Essentials\PackageTools\Package;
use Mpietrucha\Laravel\Essentials\PackageTools\PackageServiceProvider;
use Mpietrucha\Laravel\Essentials\Translation\SpatieTranslationLoaderManager;
use Mpietrucha\Support\Instance\BindExceptionHandler;
use Throwable;

class LaravelEssentialsServiceProvider extends PackageServiceProvider
{
    public function configure(Package $package): void
    {
        $package->name('laravel-essentials');

        $package->hasConfigFile('essentials');

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

        $package->hasMigration('create_discounts_table');
    }

    public function bootingPackage(): void
    {
        $this->configureExceptionHandler();
        $this->extendEloquentUserProvider();
    }

    public function registeringPackage(): void
    {
        BindExceptionHandler::dontRegister();

        config([
            'translation-loader.translation_manager' => SpatieTranslationLoaderManager::class,
        ]);
    }

    public function packageRegistered(): void
    {
        Discount::enabled() |> $this->package->runsMigrations(...);
    }

    protected function configureExceptionHandler(): void
    {
        $this->callAfterResolving(ExceptionHandler::class, static function (ExceptionHandler $exceptionHandler): void {
            $exceptionHandler->reportable(static function (Throwable $throwable): true {
                BindExceptionHandler::transform($throwable);

                return true;
            });

            $exceptionHandler->renderable(static function (Throwable $throwable): null {
                BindExceptionHandler::transform($throwable);

                return null;
            });
        });
    }

    protected function extendEloquentUserProvider(): void
    {
        auth()->provider('cached', static function (Application $application, array $config): CachedEloquentUserProvider {
            $hashManager = $application->get(HashManager::class);

            $model = Arr::string($config, 'model');

            return new CachedEloquentUserProvider($hashManager, $model);
        });
    }
}
