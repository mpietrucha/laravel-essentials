<?php

namespace Mpietrucha\Laravel\Essentials\Package;

use Mpietrucha\Laravel\Essentials\Package\Builder\Concerns\HasBladeAnonymousComponents;
use Mpietrucha\Laravel\Essentials\Package\Builder\Concerns\HasMixins;
use Mpietrucha\Support\Concerns\Makeable;
use Spatie\LaravelPackageTools\Package;

class Builder extends Package
{
    use HasBladeAnonymousComponents;
    use HasMixins;
    use Makeable;

    public function tag(): string
    {
        return $this->shortName();
    }
}
