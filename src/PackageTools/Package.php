<?php

namespace Mpietrucha\Laravel\Essentials\PackageTools;

use Mpietrucha\Laravel\Essentials\PackageTools\Package\Concerns\HasBladeAnonymousComponents;
use Mpietrucha\Laravel\Essentials\PackageTools\Package\Concerns\HasMixins;
use Mpietrucha\Support\Concerns\Makeable;

class Package extends \Spatie\LaravelPackageTools\Package
{
    use HasBladeAnonymousComponents;
    use HasMixins;
    use Makeable;

    public function tag(): string
    {
        return $this->shortName();
    }
}
