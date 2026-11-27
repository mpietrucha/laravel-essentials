<?php

namespace Mpietrucha\Laravel\Essentials\PackageTools;

use Mpietrucha\Laravel\Essentials\PackageTools\Concerns\ProcessBladeAnonymousComponents;
use Mpietrucha\Laravel\Essentials\PackageTools\Concerns\ProcessMixins;
use Mpietrucha\Laravel\Essentials\PackageTools\Concerns\Schedulable;
use Spatie\LaravelPackageTools\Package as SpatiePackage;
use Spatie\LaravelPackageTools\PackageServiceProvider as SpatiePackageServiceProvider;

/**
 * @property Package $package
 */
abstract class PackageServiceProvider extends SpatiePackageServiceProvider
{
    use ProcessBladeAnonymousComponents;
    use ProcessMixins;
    use Schedulable;

    abstract public function configure(Package $package): void;

    /**
     * @param  Package  $package
     */
    public function configurePackage(SpatiePackage $package): void
    {
        $this->configure($package);
    }

    #[\Override]
    public function newPackage(): Package
    {
        return Package::make();
    }

    #[\Override]
    protected function bootPackageViewSharedData(): static
    {
        parent::bootPackageViewSharedData();

        $this->bootPackageMixins();

        return $this->bootPackageBladeAnonymousComponents();
    }
}
