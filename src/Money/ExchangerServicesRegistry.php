<?php

namespace Mpietrucha\Laravel\Essentials\Money;

use Exchanger\Contract\ExchangeRateService;
use Illuminate\Support\Collection;
use Mpietrucha\Laravel\Essentials\Facade;
use Mpietrucha\Laravel\Essentials\Money\Exchanger\FallbackExchangerService;
use Mpietrucha\Laravel\Essentials\Money\Exchanger\FrankfurterExchangerService;
use Swap\Service\Registry;

/**
 * @phpstan-type ExchangeRateServiceClass class-string<ExchangeRateService>
 */
abstract class ExchangerServicesRegistry
{
    /**
     * @var list<ExchangeRateServiceClass>
     */
    protected static array $services = [
        FallbackExchangerService::class,
        FrankfurterExchangerService::class,
    ];

    /**
     * @return list<ExchangeRateServiceClass>
     */
    public static function getServices(): array
    {
        return static::$services;
    }

    public static function registerAll(): void
    {
        $services = static::getServices();

        static::register(...) |> Collection::make($services)->each(...);
    }

    /**
     * @param  ExchangeRateServiceClass  $serviceClass
     */
    public static function register(string $serviceClass): void
    {
        $service = Facade::for($serviceClass);

        Registry::register($service::getName(), $serviceClass);
    }
}
