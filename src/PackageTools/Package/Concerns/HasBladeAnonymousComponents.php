<?php

namespace Mpietrucha\Laravel\Essentials\PackageTools\Package\Concerns;

use Mpietrucha\Laravel\Essentials\PackageTools\Package;

/**
 * @phpstan-require-extends Package
 */
trait HasBladeAnonymousComponents
{
    public bool $hasBladeAnonymousComponents = false;

    public function hasBladeAnonymousComponents(): static
    {
        $this->hasBladeAnonymousComponents = true;

        return $this;
    }
}
