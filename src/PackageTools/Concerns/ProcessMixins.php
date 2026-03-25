<?php

namespace Mpietrucha\Laravel\Essentials\PackageTools\Concerns;

use Mpietrucha\Laravel\Essentials\Macro\Mixin;
use Mpietrucha\Laravel\Essentials\PackageTools\Package\Concerns\HasMixins;
use Mpietrucha\Laravel\Essentials\PackageTools\PackageServiceProvider;

/**
 * @phpstan-require-extends PackageServiceProvider
 *
 * @phpstan-import-type Mixins from HasMixins
 */
trait ProcessMixins
{
    /**
     * @param  null|Mixins  $mixins
     */
    protected function bootPackageMixins(?array $mixins = null): static
    {
        collect(
            $mixins ?? $this->package->mixins ?? []
        )->each(static fn (object|string $handler, string $target) => Mixin::use($target, $handler));

        return $this;
    }
}
