<?php

declare(strict_types=1);

namespace Mpietrucha\Laravel\Essentials\Saloon\Requests;

use Mpietrucha\Laravel\Essentials\Saloon\Concerns\Requests\AsPost;
use Saloon\Http\Request;

abstract class PostRequest extends Request
{
    use AsPost;
}
