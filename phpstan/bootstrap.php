<?php

use Illuminate\Support\Facades\Artisan;
use Mpietrucha\PHPStan\IdeHelpers;
use Mpietrucha\PHPStan\Laravel;
use Mpietrucha\PHPStan\MixinAnalyzers;

Laravel::bootstrap();

if (IdeHelpers::due()) {
    Artisan::call('essentials:ide-helpers');
}

if (MixinAnalyzers::due()) {
    Artisan::call('essentials:mixin-analyzers');
}
