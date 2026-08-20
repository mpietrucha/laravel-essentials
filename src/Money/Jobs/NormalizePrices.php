<?php

namespace Mpietrucha\Laravel\Essentials\Money\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Collection;
use Illuminate\Support\LazyCollection;
use Illuminate\Support\Str;
use Mpietrucha\Laravel\Essentials\Money\HasPriceAutoloader;
use Mpietrucha\Support\ClassNamespace;
use Mpietrucha\Support\Filesystem;
use Mpietrucha\Support\Finder;
use Mpietrucha\Support\Instance;

/**
 * @phpstan-type PriceNormalizerCollection Collection<int, string>
 */
class NormalizePrices implements ShouldQueue
{
    use Queueable;

    /**
     * @var null|list<string>
     */
    protected static ?array $modelDirectories = null;

    /**
     * @param  list<string>  $modelDirectories
     */
    public static function findModelsIn(array $modelDirectories): void
    {
        static::$modelDirectories = $modelDirectories;
    }

    public static function handle(): void
    {
        $models = static::getModelsWithPriceNormalizers();

        $models->each(static function (Collection $priceNormalizers, string $modelClass): void {
            $models = $modelClass::query()->lazyById();

            $models->each(static fn (Model $model) => static::normalizePrices($model, $priceNormalizers));
        });
    }

    /**
     * @return LazyCollection<class-string<Model>, PriceNormalizerCollection>
     */
    protected static function getModelsWithPriceNormalizers(): LazyCollection
    {
        $modelDirectories = static::$modelDirectories ?? app_path('Models');

        $files = Finder::make()
            ->files()
            ->name('*.php')
            ->in($modelDirectories)
            ->ignoreUnreadableDirs()
            ->get();

        /** @phpstan-ignore argument.type */
        return $files->mapWithKeys(static function (string $file): ?array {
            $modelClass = static::getModelClass($file);

            if ($modelClass === null) {
                return null;
            }

            return [$modelClass => static::getPriceNormalizers($modelClass)];
        })->filter();
    }

    /**
     * @return class-string<Model>
     */
    protected static function getModelClass(string $file): ?string
    {
        $class = Filesystem::namespace($file);

        if ($class === null) {
            return null;
        }

        return Instance::is($class, Model::class) ? $class : null;
    }

    /**
     * @return PriceNormalizerCollection
     */
    protected static function getPriceNormalizers(string $modelClass): Collection
    {
        $indicators = Instance::traits($modelClass)
            ->values()
            ->map(ClassNamespace::name(...))
            ->map(HasPriceAutoloader::getTraitIndicator(...))
            ->filter()
            ->unique()
            /** @phpstan-ignore argument.type */
            ->push(Str::none());

        /** @var PriceNormalizerCollection */
        return $indicators->map(static fn (string $indicator): string => sprintf('normalize%sPrice', $indicator));
    }

    /**
     * @param  PriceNormalizerCollection  $priceNormalizers
     */
    protected static function normalizePrices(Model $model, Collection $priceNormalizers): void
    {
        $priceNormalizers->each(static fn (string $priceNormalizer) => $model->$priceNormalizer());

        $model->save();
    }
}
