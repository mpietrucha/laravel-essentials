<?php

namespace Mpietrucha\Laravel\Essentials\Commands;

use BackedEnum;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Mpietrucha\Laravel\Essentials\Commands\Concerns\InteractsWithLint;
use Mpietrucha\Support\Enum;
use Mpietrucha\Support\Filesystem;
use Mpietrucha\Support\Str;

class GenerateColors extends Command
{
    use InteractsWithLint;

    /**
     * @var string
     */
    #[\Override]
    protected $signature = 'essentials:colors
                            {--enum=App\Enums\Color : The Color enum class to generate CSS variables from}
                            {--output=css/colors.css : The resource file to write the generated CSS to}';

    /**
     * @var string
     */
    #[\Override]
    protected $description = 'Generate CSS color variables';

    public function handle(): void
    {
        $css = Enum::backed(
            /** @var string */
            $this->option('enum')
        )::collection()->map(static function (BackedEnum $backedEnum): string {
            $name = $backedEnum->name |> Str::kebab(...);

            $value = $backedEnum->value;

            return sprintf('--color-%s: %s', $name, $value);
        })->pipe(static fn (Collection $colors): string => sprintf('@theme { %s }', Str::eol() |> $colors->join(...)));

        /** @phpstan-ignore argument.type */
        $output = $this->option('output') |> resource_path(...);

        Filesystem::put($output, $css);

        $this->lint($output);
    }
}
