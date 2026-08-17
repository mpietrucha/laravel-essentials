<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Model;

class HasPrice extends Model
{
    use Mpietrucha\Laravel\Essentials\Money\Models\Concerns\HasPrice;
}
