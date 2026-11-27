<?php

namespace Mpietrucha\Laravel\Essentials\Console\Scheduling;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Collection;
use Mpietrucha\Support\Concerns\Makeable;
use Mpietrucha\Support\Exception\RuntimeException;
use Throwable;

/**
 * @phpstan-type PendingCallArguments array<mixed>
 * @phpstan-type PendingCall array{string, PendingCallArguments}
 * @phpstan-type PendingCallCollection Collection<int, PendingCall>
 *
 * @mixin Schedule
 */
class PendingSchedule
{
    use Makeable;

    /**
     * @var null|PendingCallCollection
     */
    protected ?Collection $pendingCalls = null;

    /**
     * @param  PendingCallArguments  $arguments
     */
    public function __call(string $method, array $arguments): static
    {
        $this->getPendingCalls()->push([$method, $arguments]);

        return $this;
    }

    public function apply(Schedule $schedule): void
    {
        $this->getPendingCalls()->reduce(static function (mixed $target, array $pendingCall): mixed {
            try {
                [$method, $arguments] = $pendingCall;

                return $target->$method(...$arguments);
            } catch (Throwable $throwable) {
                RuntimeException::usePrevious($throwable);

                RuntimeException::throw('Failed to apply pending schedule calls');
            }
        }, $schedule);
    }

    /**
     * @return PendingCallCollection
     */
    protected function getPendingCalls(): Collection
    {
        if ($this->pendingCalls instanceof Collection) {
            return $this->pendingCalls;
        }

        return $this->pendingCalls = Collection::make();
    }
}
