<?php

namespace Mpietrucha\Laravel\Essentials\Console\Commands;

use BackedEnum;
use Illuminate\Support\Collection;
use Mpietrucha\Laravel\Essentials\Console\Command;
use Mpietrucha\Laravel\Essentials\Console\Commands\Concerns\InteractsWithLint;
use Mpietrucha\Support\Enum;
use Mpietrucha\Support\Filesystem;
use Mpietrucha\Support\Str;

/**
 * @phpstan-type BackedEnumCollection Collection<int, BackedEnum>
 */
class GenerateColors extends Command
{
    use InteractsWithLint;

    /**
     * @var string
     */
    #[\Override]
    protected $signature = 'essentials:generate-colors
                            {--enum=App\Enums\Color : The Color enum class to generate CSS variables from}
                            {--output=css/colors.css : The resource file to write the generated CSS to}';

    /**
     * @var string
     */
    #[\Override]
    protected $description = 'Generate CSS color variables';

    public function handle(): void
    {
        $css = $this->getEnumCollection()->map(static function (BackedEnum $backedEnum): string {
            $name = $backedEnum->name |> Str::kebab(...);

            $value = $backedEnum->value;

            return sprintf('--color-%s: %s', $name, $value);
        })->pipe($this->join(...));

        /** @phpstan-ignore argument.type */
        $output = $this->option('output') |> resource_path(...);

        Filesystem::put($output, $css);

        $this->lint($output);

        sprintf('Colors generated successfully in [%s].', $output) |> $this->info(...);
    }

    /**
     * @param  Collection<int, non-falsy-string>  $colors
     */
    protected function join(Collection $colors): string
    {
        $line = Str::eol();

        return sprintf('@theme { %s }', $colors->join($line));
    }

    /**
     * @return BackedEnumCollection
     */
    protected function getEnumCollection(): Collection
    {
        /** @var string $enum */
        $enum = $this->option('enum');

        /** @var BackedEnumCollection */
        return Enum::backed($enum)::collection();
    }
}
