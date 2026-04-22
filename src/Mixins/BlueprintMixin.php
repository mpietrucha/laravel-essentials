<?php

namespace Mpietrucha\Laravel\Essentials\Mixins;

use Illuminate\Database\Schema\Blueprint;

/**
 * @phpstan-require-extends Blueprint
 */
trait BlueprintMixin
{
    public function mixedMorphs(string $name, ?string $index = null): void
    {
        $id = sprintf('%s_id', $name);
        $type = sprintf('%s_type', $name);

        $this->char($id, 36);
        $this->string($type);

        $this->index([$id, $type], $index);
    }
}
