<?php

declare(strict_types=1);

namespace Mpietrucha\Laravel\Essentials\Money\Jobs;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Mpietrucha\Laravel\Essentials\Job;
use Mpietrucha\Laravel\Essentials\Money\Models\Discount;
use Mpietrucha\Laravel\Essentials\Money\Models\Discount\Phase;
use Mpietrucha\Laravel\Essentials\Money\Models\Discount\Quota;

/**
 * @phpstan-type PhaseModel class-string<Phase>
 * @phpstan-type PhaseBuilder Builder<Phase>
 * @phpstan-type PhaseModelCollection Collection<int, PhaseModel>
 */
class TouchStalePhases extends Job
{
    /**
     * @return PhaseModelCollection
     */
    public static function getPhaseModels(): Collection
    {
        /** @var PhaseModelCollection */
        return [
            Quota::getModel(),
            Discount::getModel(),
        ] |> Collection::make(...);
    }

    public function handle(): void
    {
        static::getPhaseModels()->each(static function (string $phaseModel): void {
            $phasesBuilder = $phaseModel::query();

            $phasesBuilder->clone() |> static::touchStartedPhases(...);
            $phasesBuilder->clone() |> static::touchExpiredPhases(...);
        });
    }

    /**
     * @param  PhaseBuilder  $phasesBuilder
     */
    protected static function touchStartedPhases(Builder $phasesBuilder): void
    {
        $phasesBuilder->active();

        /** @phpstan-ignore argument.type */
        $phasesBuilder->whereColumn($phasesBuilder->getModel()->getUpdatedAtColumn(), '<', 'active_from');

        $phasesBuilder->touch();
    }

    /**
     * @param  PhaseBuilder  $phasesBuilder
     */
    protected static function touchExpiredPhases(Builder $phasesBuilder): void
    {
        $phasesBuilder->whereNull('finished_at');

        $phasesBuilder->wherePast($activeTo = 'active_to');

        /** @phpstan-ignore argument.type */
        $phasesBuilder->whereColumn($phasesBuilder->getModel()->getUpdatedAtColumn(), '<', $activeTo);

        $phasesBuilder->touch();
    }
}
