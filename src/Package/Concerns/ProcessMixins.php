<?php

namespace Mpietrucha\Laravel\Essentials\Package\Concerns;

use Illuminate\Support\ServiceProvider;
use Mpietrucha\Laravel\Essentials\Macro\Mixin;

/**
 * @phpstan-require-extends ServiceProvider
 *
 * @phpstan-import-type Mixins from \Mpietrucha\Laravel\Essentials\Package\Builder\Concerns\HasMixins
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
        )->each(fn (string $mixin, string $source) => Mixin::use($source, $mixin));

        return $this;
    }
}
