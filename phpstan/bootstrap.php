<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Artisan;
use Mpietrucha\PHPStan\Cache;
use Mpietrucha\PHPStan\ClearResultCache;
use Mpietrucha\PHPStan\GenerateIdeHelpers;
use Mpietrucha\PHPStan\GenerateMixinAnalyzers;
use Mpietrucha\PHPStan\Laravel;
use Mpietrucha\PHPStan\Lock;

Laravel::bootstrap();

if (ClearResultCache::due()) {
    Lock::flush();
    Cache::flush();
}

$lock = Lock::acquire();

if (GenerateIdeHelpers::due()) {
    Artisan::call('essentials:generate-ide-helpers');
}

if (GenerateMixinAnalyzers::due()) {
    Artisan::call('essentials:generate-mixin-analyzers');
}

$lock->release();
