<?php

declare(strict_types=1);

namespace Mpietrucha\Laravel\Essentials\Enums;

use Mpietrucha\Support\Enums\Concerns\InteractsWithEnum;
use Mpietrucha\Support\Enums\Contracts\EnumInterface;

enum DiscountType: string implements EnumInterface
{
    use InteractsWithEnum;

    case Promotion = 'promotion';

    case Bundle = 'bundle';
}
