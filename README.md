# Laravel Package

[![Latest Version on Packagist](https://img.shields.io/packagist/v/mpietrucha/laravel-package.svg?style=flat-square)](https://packagist.org/packages/mpietrucha/laravel-package)
[![Total Downloads](https://img.shields.io/packagist/dt/mpietrucha/laravel-package.svg?style=flat-square)](https://packagist.org/packages/mpietrucha/laravel-package)
[![MIT Licensed](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)

A powerful toolkit for Laravel package development that simplifies common package patterns including service provider setup, macro/mixin registration, context detection, and translation helpers.

## Installation

> **Requires [PHP 8.5+](https://php.net/releases/) and [Laravel 12.37+](https://laravel.com)**

You can install the package via Composer:

```bash
composer require mpietrucha/laravel-package
```

## Usage

### Service Provider

Extend the `ServiceProvider` class for enhanced package configuration:

```php
use Mpietrucha\Laravel\Package\ServiceProvider;
use Mpietrucha\Laravel\Package\Builder;

class MyPackageServiceProvider extends ServiceProvider
{
    public function configure(Builder $package): void
    {
        $package
            ->name('my-package')
            ->hasConfigFile()
            ->hasViews()
            ->hasTranslations();
    }
}
```

### Macros

Register macros with built-in error handling and validation:

```php
use Mpietrucha\Laravel\Package\Macro;
use Illuminate\Support\Collection;

Macro::attach(
    destination: Collection::class,
    name: 'sum',
    handler: fn() => $this->reduce(fn($carry, $item) => $carry + $item, 0)
);
```

### Mixins

Register multiple methods at once using mixins:

```php
use Mpietrucha\Laravel\Package\Mixin;
use Illuminate\Support\Collection;

// Using a trait
trait CollectionHelpers
{
    public function sumAll(): int
    {
        return $this->sum();
    }

    public function avgAll(): float
    {
        return $this->avg();
    }
}

Mixin::attach(Collection::class, CollectionHelpers::class);

// Using an object
class CollectionMixin
{
    public function double()
    {
        return $this->map(fn($item) => $item * 2);
    }

    public function positive()
    {
        return $this->filter(fn($item) => $item > 0);
    }
}

Mixin::attach(Collection::class, new CollectionMixin());
```

### Context Detection

Automatically detect information about your package:

```php
use Mpietrucha\Laravel\Package\Context;

$name = Context::name();
$directory = Context::directory();
$provider = Context::provider();
```

### Translations

Use scoped translation helpers in your package:

```php
use Mpietrucha\Laravel\Package\Translations\Concerns\InteractsWithTranslations;

class MyClass
{
    use InteractsWithTranslations;

    public function getMessage(): string
    {
        // Automatically scopes to 'my-package::messages.welcome'
        return static::__('messages.welcome');
    }

    public function getGreeting(string $name): string
    {
        // Equivalent to __('my-package::messages.hello', ['name' => $name])
        return static::__('messages.hello', ['name' => $name]);
    }
}
```

## License

MIT
