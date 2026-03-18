<?php

namespace Mpietrucha\Laravel\Essentials\Package\Builder\Concerns;

use Mpietrucha\Laravel\Essentials\Package\Builder;

/**
 * @phpstan-require-extends Builder
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
