<?php

namespace Mpietrucha\Laravel\Essentials\Package\Concerns;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Mpietrucha\Support\Filesystem\Path;

/**
 * @phpstan-require-extends ServiceProvider
 */
trait ProcessBladeAnonymousComponents
{
    protected function bootPackageBladeAnonymousComponents(): static
    {
        if (! $this->package()->hasBladeAnonymousComponents) {
            return $this;
        }

        $components = Path::build('../resources/views/components', $this->getPackageBaseDir());

        $prefix = $this->package()->tag();

        Blade::anonymousComponentPath($components, $prefix);

        return $this;
    }
}
