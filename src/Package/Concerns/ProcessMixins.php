<?php

namespace Mpietrucha\Laravel\Essentials\Package\Concerns;

use Illuminate\Support\ServiceProvider;
use Mpietrucha\Laravel\Essentials\Macro\Mixin;
use Mpietrucha\Laravel\Essentials\Package\Builder\Concerns\HasMixins;

/**
 * @phpstan-require-extends ServiceProvider
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
            $mixins ?? $this->package()->mixins ?? []
        )->each(fn (object|string $handler, string $target) => Mixin::use($target, $handler));

        return $this;
    }
}
