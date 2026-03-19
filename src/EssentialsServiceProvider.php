<?php

namespace Mpietrucha\Laravel\Essentials;

use Mpietrucha\Laravel\Essentials\Commands\GenerateIdeHelpers;
use Mpietrucha\Laravel\Essentials\Commands\GenerateMixinAnalyzers;
use Mpietrucha\Laravel\Essentials\Commands\Lint;
use Mpietrucha\Laravel\Essentials\Package\Builder;
use Mpietrucha\Laravel\Essentials\Package\ServiceProvider;

class EssentialsServiceProvider extends ServiceProvider
{
    public function configure(Builder $builder): void
    {
        $builder->name('laravel-essentials');

        $builder->hasConfigFile('app');

        $builder->hasConsoleCommands([
            Lint::class,
            GenerateIdeHelpers::class,
            GenerateMixinAnalyzers::class,
        ]);
    }
}
