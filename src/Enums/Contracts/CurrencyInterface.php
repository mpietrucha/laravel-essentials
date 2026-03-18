<?php

namespace Mpietrucha\Laravel\Essentials\Enums\Contracts;

interface CurrencyInterface extends LocaleInterface
{
    public function symbol(): string;
}
