<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Model;

class HasMoney extends Model
{
    use Mpietrucha\Laravel\Essentials\Discounts\Models\Concerns\HasMoney;
}
