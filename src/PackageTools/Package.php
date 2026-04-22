<?php

declare(strict_types=1);

namespace Mpietrucha\Laravel\Essentials\PackageTools;

use Mpietrucha\Laravel\Essentials\PackageTools\Package\Concerns\HasBladeAnonymousComponents;
use Mpietrucha\Laravel\Essentials\PackageTools\Package\Concerns\HasMixins;
use Mpietrucha\Support\Concerns\Makeable;
use Spatie\LaravelPackageTools\Package as SpatiePackage;

class Package extends SpatiePackage
{
    use HasBladeAnonymousComponents;
    use HasMixins;
    use Makeable;

    public function tag(): string
    {
        return $this->shortName();
    }
}
