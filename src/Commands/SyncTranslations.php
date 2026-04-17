<?php

namespace Mpietrucha\Laravel\Essentials\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Mpietrucha\Laravel\Essentials\Locale;
use Mpietrucha\Support\Filesystem;
use Mpietrucha\Support\Finder;
use Spatie\TranslationLoader\LanguageLine;
use Symfony\Component\Finder\SplFileInfo;

/**
 * @phpstan-type LanguageLineCollection Collection<string, LanguageLine>
 */
class SyncTranslations extends Command
{
    /**
     * @var string
     */
    #[\Override]
    protected $signature = 'essentials:translations';

    /**
     * @var string
     */
    #[\Override]
    protected $description = 'Sync file translations into database';

    /**
     * @var LanguageLineCollection|null
     */
    protected ?Collection $languageLines = null;

    public function handle(): void
    {
        Locale::enum()::collection()
            ->map
            ->code()
            ->each(function (string $languageCode): void {
                Finder::make()
                    ->in(lang_path())
                    ->files()
                    ->path($languageCode)
                    ->name(['*.php', '*.json'])
                    ->get()
                    ->each(function (SplFileInfo $file) use ($languageCode): void {
                        $group = match (true) {
                            $file->getExtension() === 'json' => '*',
                            default => $file->getFilenameWithoutExtension()
                        };

                        /** @var array<string, mixed> */
                        $translations = match (true) { /** @phpstan-ignore match.unhandled */
                            $file->getExtension() === 'json' => Filesystem::json($file),
                            $file->getExtension() === 'php' => Filesystem::getRequire($file),
                        };

                        $this->sync($group, $languageCode, $translations);
                    });
            });

        $synced = $this
            ->languageLines()
            ->filter
            ->isDirty()
            ->each
            ->save()
            ->count();

        if ($synced === 0) {
            $this->info('All translations synced successfully');

            return;
        }

        sprintf('%s translation key(s) synced successfully', $synced) |> $this->info(...);
    }

    /**
     * @param  array<string, mixed>  $translations
     */
    protected function sync(string $group, string $languageCode, array $translations): void
    {
        /** @var Collection<string, string> $translations */
        $translations = collect($translations)
            ->dot()
            /** @phpstan-ignore argument.type */
            ->ensure('string');

        $translations->each(function (string $text, string $key) use ($group, $languageCode): void {
            $languageLine = $this->languageLine($group, $key);

            if ($languageLine->getTranslation($languageCode)) {
                return;
            }

            $languageLine->setTranslation($languageCode, $text);
        });
    }

    protected function languageLine(string $group, string $key): LanguageLine
    {
        $indicator = sprintf('%s-%s', $group, $key);

        $attributes = [
            'key' => $key,
            'group' => $group,
        ];

        return $this->languageLines()->getOrPut(
            $indicator,
            static fn (): LanguageLine => LanguageLine::query()->firstOrNew($attributes)
        );
    }

    /**
     * @return LanguageLineCollection
     */
    protected function languageLines(): Collection
    {
        return $this->languageLines ??= collect();
    }
}
