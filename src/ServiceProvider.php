<?php

namespace Mpietrucha\Laravel\Package;

use Illuminate\Contracts\Foundation\Application;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

/**
 * @property \Mpietrucha\Laravel\Package\Builder $package
 */
abstract class ServiceProvider extends PackageServiceProvider
{
    abstract public function configure(Builder $package): void;

    /**
     * @param  \Mpietrucha\Laravel\Package\Builder  $package
     */
    public function configurePackage(Package $package): void
    {
        $this->configure($package);
    }

    public function newPackage(): Builder
    {
        return Builder::create();
    }

    public function package(): Builder
    {
        return $this->package;
    }

    public function app(): Application
    {
        return $this->app;
    }
}
