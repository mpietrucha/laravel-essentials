<?php

namespace Mpietrucha\Laravel\Essentials\Mixins;

use Illuminate\Database\Schema\Blueprint;

/**
 * @phpstan-require-extends Blueprint
 */
trait BlueprintMixin
{
    public function mixedMorphs(string $name, ?string $index = null, bool $nullable = false): void
    {
        $id = sprintf('%s_id', $name);
        $type = sprintf('%s_type', $name);

        $this->string($type)->nullable($nullable);
        $this->string($id, 36)->nullable($nullable);

        $this->index([$type, $id], $index);
    }

    public function nullableMixedMorphs(string $name, ?string $index = null): void
    {
        $this->mixedMorphs($name, $index, true);
    }
}
