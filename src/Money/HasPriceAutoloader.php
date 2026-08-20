<?php

declare(strict_types=1);

namespace Mpietrucha\Laravel\Essentials\Money;

use Mpietrucha\Laravel\Essentials\Money\Models\Concerns\HasPrice;
use Mpietrucha\Support\ClassNamespace;
use Mpietrucha\Support\Filesystem;
use Mpietrucha\Support\Filesystem\Path;
use Mpietrucha\Support\Filesystem\Temporary;
use Mpietrucha\Support\Filesystem\Touch;
use Mpietrucha\Support\Str;
use Mpietrucha\Support\Stubs\StubRenderer;

class HasPriceAutoloader
{
    protected static bool $initialized = false;

    protected static ?string $stubFile = null;

    protected static ?string $traitNamespace = null;

    public static function stubFile(string $stubFile): void
    {
        static::$stubFile = $stubFile;
    }

    public static function traitNamespace(string $traitNamespace): void
    {
        static::$traitNamespace = $traitNamespace;
    }

    public static function getStubFile(): string
    {
        if ($stubFile = static::$stubFile) {
            return $stubFile;
        }

        return static::$stubFile = Path::build('stubs/model.has-price.stub', __DIR__);
    }

    public static function getTraitNamespace(): string
    {
        if ($traitNamespace = static::$traitNamespace) {
            return $traitNamespace;
        }

        return static::$traitNamespace = ClassNamespace::parent(HasPrice::class);
    }

    public static function getTraitIndicator(string $trait): ?string
    {
        if (Str::doesntStartWith($trait, $start = 'Has')) {
            return null;
        }

        if (Str::doesntEndWith($trait, $finish = 'Price')) {
            return null;
        }

        return Str::between($trait, $start, $finish) |> Str::studly(...) |> Str::nullWhenEmpty(...);
    }

    public static function register(): void
    {
        if (static::$initialized) {
            return;
        }

        static::autoload(...) |> spl_autoload_register(...);

        static::$initialized = true;
    }

    protected static function autoload(string $trait): void
    {
        $file = Temporary::path(ClassNamespace::toFile($trait), 'laravel.model.has-price');

        if (Filesystem::exists($file)) {
            Filesystem::getRequire($file);

            return;
        }

        if (ClassNamespace::parent($trait) !== static::getTraitNamespace()) {
            return;
        }

        $content = static::renderStub($trait);

        if ($content === null) {
            return;
        }

        Touch::file($file);

        Filesystem::put($file, $content) && Filesystem::getRequire($file);
    }

    protected static function renderStub(string $trait): ?string
    {
        $indicator = static::getTraitIndicator($trait = ClassNamespace::name($trait));

        if ($indicator === null) {
            return null;
        }

        return StubRenderer::file(static::getStubFile(), [
            'trait' => $trait,
            'indicator' => $indicator,
            'name' => Str::camel($indicator),
            'attribute' => Str::snake($indicator),
            'namespace' => static::getTraitNamespace(),
        ]);
    }
}
