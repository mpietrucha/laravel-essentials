<?php

namespace Mpietrucha\Laravel\Essentials\Console\Scheduling;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Collection;
use Mpietrucha\Support\Concerns\Makeable;
use Mpietrucha\Support\Exception\RuntimeException;
use Throwable;

/**
 * @phpstan-type PendingCallArguments array<mixed>
 * @phpstan-type PendingCallCollection Collection<string, PendingCallArguments>
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
        $this->getPendingCalls()->put($method, $arguments);

        return $this;
    }

    public function apply(Schedule $schedule): void
    {
        $this->getPendingCalls()->each(static function (array $arguments, string $method) use ($schedule): void {
            try {
                $schedule->$method(...$arguments);
            } catch (Throwable $throwable) {
                RuntimeException::usePrevious($throwable);

                RuntimeException::throw('Failed to apply pending schedule calls');
            }
        });
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
