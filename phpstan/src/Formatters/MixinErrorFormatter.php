<?php

declare(strict_types=1);

namespace Mpietrucha\PHPStan\Formatters;

use Illuminate\Support\Arr;
use Mpietrucha\Laravel\Essentials\Macro\MixinAnalyzer;
use Mpietrucha\Support\Str;
use PHPStan\Analyser\Error;
use PHPStan\Command\AnalysisResult;
use PHPStan\Command\ErrorFormatter\ErrorFormatter;
use PHPStan\Command\ErrorFormatter\TableErrorFormatter;
use PHPStan\Command\Output;

/**
 * @internal
 */
final readonly class MixinErrorFormatter implements ErrorFormatter
{
    public function __construct(private TableErrorFormatter $tableErrorFormatter)
    {
    }

    public function formatErrors(AnalysisResult $result, Output $output): int
    {
        $result = $this->rewrite($result);

        return $this->tableErrorFormatter->formatErrors($result, $output);
    }

    private function rewrite(AnalysisResult $analysisResult): AnalysisResult
    {
        /** @var list<Error> $errors */
        $errors = Arr::map($analysisResult->getFileSpecificErrors(), $this->error(...));

        /** @phpstan-ignore phpstanApi.constructor */
        return new AnalysisResult(
            $errors,
            $analysisResult->getNotFileSpecificErrors(),
            $analysisResult->getInternalErrorObjects(),
            $analysisResult->getWarnings(),
            $analysisResult->getCollectedData(),
            $analysisResult->isDefaultLevelUsed(),
            $analysisResult->getProjectConfigFile(),
            $analysisResult->isResultCacheSaved(),
            $analysisResult->getPeakMemoryUsageBytes(),
            $analysisResult->isResultCacheUsed(),
            $analysisResult->getChangedProjectExtensionFilesOutsideOfAnalysedPaths()
        );
    }

    private function error(Error $error): Error
    {
        $indicator = MixinAnalyzer::indicator();

        /** @phpstan-ignore phpstanApi.constructor */
        return new Error(
            Str::remove($indicator, $error->getMessage()),
            Str::remove($indicator, $error->getFile()),
            $error->getLine(),
            $error->canBeIgnored(),
            $error->getFilePath(),
            $error->getTraitFilePath(),
            $error->getTip(),
            $error->getNodeLine(),
            $error->getNodeType(),
            $error->getIdentifier(),
            $error->getMetadata(),
            $error->getFixedErrorDiff()
        );
    }
}
