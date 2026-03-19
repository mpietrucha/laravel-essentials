<?php

namespace Mpietrucha\Laravel\Essentials\Package;

use Illuminate\Contracts\Foundation\Application;
use Mpietrucha\Laravel\Essentials\Package\Concerns\ProcessBladeAnonymousComponents;
use Mpietrucha\Laravel\Essentials\Package\Concerns\ProcessMixins;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

/**
 * @property Builder $package
 */
abstract class ServiceProvider extends PackageServiceProvider
{
    use ProcessBladeAnonymousComponents;
    use ProcessMixins;

    abstract public function configure(Builder $builder): void;

    /**
     * @param  Builder  $package
     */
    public function configurePackage(Package $package): void
    {
        $this->configure($package);
    }

    #[\Override]
    public function newPackage(): Builder
    {
        return Builder::make();
    }

    public function package(): Builder
    {
        return $this->package;
    }

    public function app(): Application
    {
        return $this->app;
    }

    #[\Override]
    protected function bootPackageViewSharedData(): static
    {
        parent::bootPackageViewSharedData();

        return $this
            ->bootPackageMixins()
            ->bootPackageBladeAnonymousComponents();
    }
}
