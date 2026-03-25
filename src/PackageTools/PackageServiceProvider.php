<?php

namespace Mpietrucha\Laravel\Essentials\PackageTools;

use Mpietrucha\Laravel\Essentials\PackageTools\Concerns\ProcessBladeAnonymousComponents;
use Mpietrucha\Laravel\Essentials\PackageTools\Concerns\ProcessMixins;
use Spatie\LaravelPackageTools\Package as SpatiePackage;

/**
 * @property Package $package
 */
abstract class PackageServiceProvider extends \Spatie\LaravelPackageTools\PackageServiceProvider
{
    use ProcessBladeAnonymousComponents;
    use ProcessMixins;

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
}
