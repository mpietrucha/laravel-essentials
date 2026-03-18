<?php

namespace Mpietrucha\Laravel\Essentials;

use Mpietrucha\Laravel\Essentials\Package\Builder;
use Mpietrucha\Laravel\Essentials\Package\ServiceProvider;

class EssentialsServiceProvider extends ServiceProvider
{
    public function configure(Builder $package): void
    {
        // $package
        //     ->name('laravel-essentials')
        //     ->hasConfigFile('app')
        //     ->hasConsoleCommands([
        //         Lint::class,
        //         GenerateIdeHelpers::class,
        //         GenerateMixinAnalyzers::class,
        //     ]);
    }
}
