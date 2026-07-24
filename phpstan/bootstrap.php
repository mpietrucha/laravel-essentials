<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Artisan;
use Mpietrucha\PHPStan\Cache;
use Mpietrucha\PHPStan\ClearResultCache;
use Mpietrucha\PHPStan\GenerateIdeHelpers;
use Mpietrucha\PHPStan\GenerateMixinAnalyzers;
use Mpietrucha\PHPStan\Laravel;

Laravel::bootstrap();

if (ClearResultCache::due()) {
    Cache::flush();
}

if (GenerateIdeHelpers::due()) {
    Artisan::call('essentials:generate-ide-helpers');
}

if (GenerateMixinAnalyzers::due()) {
    Artisan::call('essentials:generate-mixin-analyzers');
}
