<p align="center"><img src="/art/header.png" alt="Laravel Essentials"></p>

# Laravel Essentials

**A toolkit for Laravel package development — type-safe macros with PHPStan support, a streamlined service provider, and automatic package-scoped translations.**

Larastan covers Laravel's own `Macroable` — but Filament, Spatie, and other Macroable implementations have no static analysis support. This package fills that gap. Define your macros as traits, register them with a single line, and get complete PHPStan coverage across any Macroable implementation with zero `@method` annotations.

```php
Mixin::use(Field::class, FilamentFieldMixin::class);
```

That's it. Every public method in the trait becomes a macro on `Field` and all its child classes. PHPStan understands the signatures, parameters, and return types automatically.

## Requirements

- PHP 8.5+
- Laravel 12+

## Installation

```bash
composer require mpietrucha/laravel-essentials
```

The package auto-discovers its service provider. No additional setup needed.

## Mixins

The most expressive way to extend classes. Write a trait, register it, and you're done.

### Defining a Mixin

```php
namespace App\Mixins;

/**
 * @phpstan-require-extends \Filament\Forms\Components\Field
 */
trait TranslatableField
{
    public function translate(): static
    {
        return $this->translatable(
            defaultLocale: config('app.fallback_locale'),
        );
    }
}
```

The `@phpstan-require-extends` annotation tells PHPStan which class `$this` refers to inside the trait. This gives you full autocompletion and type checking while writing the mixin itself.

### Registering a Mixin

```php
use Mpietrucha\Laravel\Essentials\Mixin;

class FilamentServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Mixin::use(Field::class, TranslatableField::class);
    }
}
```

All public methods from the trait are registered as macros. Child classes inherit them — call `->translate()` on `Textarea`, `Select`, or any other `Field` subclass.

### Registering a Single Macro

When you don't need a full trait:

```php
use Mpietrucha\Laravel\Essentials\Macro;

Macro::use(Field::class, 'translate', function (): static {
    return $this->translatable(
        defaultLocale: config('app.fallback_locale'),
    );
});
```

### Supported Macroable Implementations

The package works with multiple `Macroable` traits out of the box:

| Trait | Source |
|---|---|
| `Illuminate\Support\Traits\Macroable` | Laravel |
| `Filament\Support\Concerns\Macroable` | Filament |
| `Spatie\Macroable\Macroable` | Spatie |

Register your own:

```php
use Mpietrucha\Laravel\Essentials\Macro\Implementation;

Implementation::use(CustomMacroable::class);
```

### Better Error Messages

When a macro throws an exception, PHP normally shows the error as originating from an anonymous closure — something like `{closure:/path/to/file.php:42}`. The package intercepts this and rewrites the trace to show the actual macro method name, so you can immediately see where the problem is.

## PHPStan

The package ships with a `MethodsClassReflectionExtension` that teaches PHPStan about every registered macro — their existence, parameters, and return types. No stubs, no `@method` blocks, no maintenance burden.

### Setup

Include the extension in your `phpstan.neon`:

```neon
includes:
    - vendor/mpietrucha/laravel-essentials/extension.neon
```

This registers the extension, adds the bootstrap file, and configures analyzer paths. Nothing else to configure.

### How It Works

During the PHPStan bootstrap, the Laravel application boots normally. Your service providers run, macros and mixins are registered, and the package records every registration in an internal map. The PHPStan extension then consults this map to resolve method calls — walking the class hierarchy so child classes inherit macros from their parents.

### Coexistence with Larastan

The extension distinguishes between **internal** and **external** Macroable implementations:

- **Internal** (`Illuminate\Support\Traits\Macroable`) — handled by Larastan, skipped by this extension
- **External** (`Filament\Support\Concerns\Macroable`, `Spatie\Macroable\Macroable`) — handled by this extension

This prevents duplicate resolution and ensures both extensions work together without conflicts.

### Trait Analysis

PHPStan does not analyze traits in isolation — it needs a concrete class context. The package solves this with the `mixin:analyzers` command, which generates thin wrapper classes:

```php
// Generated automatically in phpstan/cache/
class Field extends \Filament\Forms\Components\Field
{
    use \App\Mixins\TranslatableField;
}
```

These files give PHPStan a concrete context to type-check the trait body against. The command runs automatically during PHPStan's bootstrap phase, so generated files are always up to date.

## Package Development Utilities

For authors building Laravel packages, the library includes a base service provider and automatic translations.

### Service Provider

A typed wrapper around Spatie's `PackageServiceProvider` with a cleaner `configure` method:

```php
use Mpietrucha\Laravel\Essentials\Package\Builder;
use Mpietrucha\Laravel\Essentials\Package\ServiceProvider;

class MyPackageServiceProvider extends ServiceProvider
{
    public function configure(Builder $package): void
    {
        $package->name('my-package');

        $package->hasConsoleCommand(MyCommand::class);
    }
}
```

### Translations

The `InteractsWithTranslations` trait automatically resolves package-scoped translation keys:

```php
use Mpietrucha\Laravel\Essentials\Package\Translations\Concerns\InteractsWithTranslations;

class MyService
{
    use InteractsWithTranslations;

    public function label(): string
    {
        // Resolves to "my-package::labels.name"
        return static::__('labels.name');
    }
}
```

The package name is determined automatically from the call stack — no hardcoded strings, no configuration. This works for any class located within your package's directory structure.

> **Note:** The automatic resolution relies on backtrace introspection. It is designed for direct calls from within your package's classes. Indirect call paths (e.g. queued closures, dynamic dispatch) may not resolve correctly.

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Credits

- [Michal Pietrucha](https://github.com/mpietrucha)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
