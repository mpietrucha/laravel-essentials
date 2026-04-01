<?php

namespace Mpietrucha\Laravel\Essentials\Translation;

use Illuminate\Filesystem\Filesystem;
use Spatie\TranslationLoader\TranslationLoaderManager;

class SpatieTranslationLoaderManager extends TranslationLoaderManager
{
    /**
     * @param  string|array<string>  $path
     */
    public function __construct(Filesystem $files, array|string $path)
    {
        parent::__construct($files, $path);

        static::getLaravelInternalLangDirectory() |> $this->addPath(...);
    }

    public static function getLaravelInternalLangDirectory(): string
    {
        return base_path('vendor/laravel/framework/src/Illuminate/Translation/lang');
    }
}
