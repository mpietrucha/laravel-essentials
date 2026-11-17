<?php

namespace Mpietrucha\Laravel\Essentials\DataTransferObject\Concerns;

use Mpietrucha\Laravel\Essentials\DataTransferObject;

/**
 * @phpstan-require-extends DataTransferObject
 */
trait Serializable
{
    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    public function toJson(int $options = 0): string
    {
        return json_encode($this->jsonSerialize(), $options | JSON_THROW_ON_ERROR);
    }
}
