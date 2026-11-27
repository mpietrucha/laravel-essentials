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
            $phaseBuilder = $phaseModel::query();

            $phaseBuilder->clone() |> static::touchStartedPhases(...);
            $phaseBuilder->clone() |> static::touchExpiredPhases(...);
        });
    }

    /**
     * @param  PhaseBuilder  $phaseBuilder
     */
    protected static function touchStartedPhases(Builder $phaseBuilder): void
    {
        $phaseBuilder->active();

        /** @phpstan-ignore argument.type */
        $phaseBuilder->whereColumn($phaseBuilder->getModel()->getUpdatedAtColumn(), '<', 'active_from');

        $phaseBuilder->touch();
    }

    /**
     * @param  PhaseBuilder  $phaseBuilder
     */
    protected static function touchExpiredPhases(Builder $phaseBuilder): void
    {
        $phaseBuilder->whereNull('finished_at');

        $phaseBuilder->wherePast($activeTo = 'active_to');

        /** @phpstan-ignore argument.type */
        $phaseBuilder->whereColumn($phaseBuilder->getModel()->getUpdatedAtColumn(), '<', $activeTo);

        $phaseBuilder->touch();
    }
}
