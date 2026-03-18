<?php

namespace Mpietrucha\Laravel\Essentials\Package\Builder\Concerns;

use Mpietrucha\Laravel\Essentials\Package\Builder;

/**
 * @phpstan-type Mixins array<class-string, class-string>
 *
 * @phpstan-require-extends Builder
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
