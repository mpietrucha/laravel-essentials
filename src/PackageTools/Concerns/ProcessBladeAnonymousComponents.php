<?php

namespace Mpietrucha\Laravel\Essentials\PackageTools\Concerns;

use Illuminate\Support\Facades\Blade;
use Mpietrucha\Laravel\Essentials\PackageTools\PackageServiceProvider;
use Mpietrucha\Support\Filesystem\Path;

/**
 * @phpstan-require-extends PackageServiceProvider
 */
trait ProcessBladeAnonymousComponents
{
    protected function bootPackageBladeAnonymousComponents(): static
    {
        if (! $this->package->hasBladeAnonymousComponents) {
            return $this;
        }

        $components = Path::build('../resources/views/components', $this->getPackageBaseDir());

        $prefix = $this->package->tag();

        Blade::anonymousComponentPath($components, $prefix);

        return $this;
    }
}
