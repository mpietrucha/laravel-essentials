<?php

namespace Mpietrucha\Laravel\Package;

use Mpietrucha\Utility\Concerns\Creatable;
use Mpietrucha\Utility\Contracts\CreatableInterface;
use Spatie\LaravelPackageTools\Package;

class Builder extends Package implements CreatableInterface
{
    use Creatable;

    public function tag(): string
    {
        return $this->shortName();
    }
}
