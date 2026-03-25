<?php

namespace Mpietrucha\Laravel\Essentials\PackageTools\Package\Concerns;

use Mpietrucha\Laravel\Essentials\Macro\Mixin;
use Mpietrucha\Laravel\Essentials\PackageTools\Package;

/**
 * @phpstan-import-type MixinTarget from Mixin
 * @phpstan-import-type MixinHandler from Mixin
 *
 * @phpstan-type Mixins array<MixinTarget, MixinHandler>
 *
 * @phpstan-require-extends Package
 */
trait HasMixins
{
    /**
     * @var null|Mixins
     */
    public ?array $mixins = null;

    /**
     * @param  Mixins  $mixins
     */
    public function hasMixins(array $mixins): static
    {
        $this->mixins = $mixins;

        return $this;
    }
}
