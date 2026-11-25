<?php

declare(strict_types=1);

namespace Mpietrucha\Laravel\Essentials\Money\Exchanger\Concerns;

use Exchanger\Contract\ExchangeRateService;
use Mpietrucha\Laravel\Essentials\Money\ExchangerServicesRegistry;
use Mpietrucha\Support\Instance;
use Mpietrucha\Support\Str;

/**
 * @phpstan-require-implements ExchangeRateService
 */
trait CanBeRegistered
{
    public static function register(): void
    {
        get_called_class() |> ExchangerServicesRegistry::register(...);
    }

    public function getName(): string
    {
        return Str::before(Instance::name($this), 'ExchangerService') |> Str::kebab(...);
    }
}
