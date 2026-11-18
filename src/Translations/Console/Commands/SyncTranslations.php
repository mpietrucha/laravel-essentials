<?php

namespace Mpietrucha\Laravel\Essentials\Translations\Console\Commands;

use Illuminate\Support\Collection;
use Illuminate\Support\LazyCollection;
use Mpietrucha\Laravel\Essentials\Console\Command;
use Mpietrucha\Laravel\Essentials\Locale;
use Mpietrucha\Laravel\Essentials\Locale\Enums\Contracts\LocaleInterface;
use Mpietrucha\Support\Filesystem;
use Mpietrucha\Support\Finder;
use Spatie\TranslationLoader\LanguageLine;
use Symfony\Component\Finder\SplFileInfo;

/**
 * @phpstan-type FileTranslations array<string, mixed>
 * @phpstan-type LanguageLineCollection Collection<string, LanguageLine>
 */
class SyncTranslations extends Command
{
    /**
     * @var string
     */
    #[\Override]
    protected $signature = 'essentials:sync-translations';

    /**
     * @var null|LanguageLineCollection
     */
    protected ?Collection $languageLines = null;

    /**
     * @var string
     */
    #[\Override]
    protected $description = 'Synchronize file translations into database';

    public function handle(): void
    {
        $this->syncLanguage(...) |> $this->getLocaleCollection()->each(...);

        $syncedLanguageKeys = $this->getLanguageLines()
            ->filter
            ->isDirty()
            ->each
            ->save();

        if ($syncedLanguageKeys->isEmpty()) {
            $this->info('All translations synced successfully.');

            return;
        }

        sprintf(
            '%s translation key(s) synced successfully.', $syncedLanguageKeys->count()
        ) |> $this->info(...);
    }

    protected function syncLanguage(LocaleInterface $locale): void
    {
        $languageCode = $locale->code();

        $this->getLanguageFiles($languageCode)->each(fn (SplFileInfo $languageFile) => $this->syncLanguageFile(
            $this->getLanguageFileGroup($languageFile),
            $languageCode,
            $this->getLanguageFileTranslations($languageFile),
        ));
    }

    /**
     * @return LazyCollection<string, SplFileInfo>
     */
    protected function getLanguageFiles(string $languageCode): LazyCollection
    {
        $finder = lang_path() |> Finder::make()->in(...);

        $finder->files();

        return $finder->path($languageCode)->get();
    }

    protected function getLanguageFileGroup(SplFileInfo $languageFile): string
    {
        if ($languageFile->getExtension() === 'json') {
            return '*';
        }

        return $languageFile->getFilenameWithoutExtension();
    }

    /**
     * @return FileTranslations
     */
    protected function getLanguageFileTranslations(SplFileInfo $languageFile): array
    {
        $extension = $languageFile->getExtension();

        /** @var FileTranslations */
        return match (true) {
            $extension === 'php' => Filesystem::getRequire($languageFile),
            $extension === 'json' => Filesystem::json($languageFile),
            default => []
        };
    }

    /**
     * @param  FileTranslations  $fileTranslations
     */
    protected function syncLanguageFile(string $fileGroup, string $languageCode, array $fileTranslations): void
    {
        /** @var Collection<string, string> $fileTranslations */
        $fileTranslations = Collection::make($fileTranslations)
            ->dot()
            /** @phpstan-ignore argument.type */
            ->ensure('string');

        $fileTranslations->each(function (string $value, string $languageKey) use ($fileGroup, $languageCode): void {
            $languageLine = $this->getLanguageLine($fileGroup, $languageKey);

            if ($languageLine->getTranslation($languageCode)) {
                return;
            }

            $languageLine->setTranslation($languageCode, $value);
        });
    }

    protected function getLanguageLine(string $fileGroup, string $languageKey): LanguageLine
    {
        $indicator = sprintf('%s-%s', $fileGroup, $languageKey);

        $attributes = [
            'key' => $languageKey,
            'group' => $fileGroup,
        ];

        return $this->getLanguageLines()->getOrPut(
            $indicator,
            static fn (): LanguageLine => LanguageLine::query()->firstOrNew($attributes)
        );
    }

    /**
     * @return Collection<int, LocaleInterface>
     */
    protected function getLocaleCollection(): Collection
    {
        $localeEnum = Locale::enum();

        return $localeEnum::collection();
    }

    /**
     * @return LanguageLineCollection
     */
    protected function getLanguageLines(): Collection
    {
        if ($this->languageLines instanceof Collection) {
            return $this->languageLines;
        }

        return $this->languageLines = Collection::empty();
    }
}
