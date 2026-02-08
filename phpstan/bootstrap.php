<?php

use Illuminate\Support\Facades\Artisan;
use Mpietrucha\Utility\Constant;
use Mpietrucha\Utility\Filesystem;
use Mpietrucha\Utility\Filesystem\Path;

Constant::undefined('LARAVEL_START') && Path::build('vendor/larastan/larastan/bootstrap.php') |> Filesystem::requireOnce(...);

Artisan::call('ide:helpers');
Artisan::call('mixin:analyzers');
