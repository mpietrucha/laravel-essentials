<?php

declare(strict_types=1);

namespace Mpietrucha\Laravel\Essentials\Money\CurrencyConverter\Concerns;

use Exchanger\Contract\ExchangeRateService;
use Mpietrucha\Laravel\Essentials\Facade;
use Swap\Service\Registry;

/**
 * @phpstan-require-implements ExchangeRateService
 */
trait CanBeRegistered
{
    public function register(): void
    {
        $serviceClass = get_called_class();

        $service = Facade::for($serviceClass);

        Registry::register($service::getName(), $serviceClass);
    }
}
