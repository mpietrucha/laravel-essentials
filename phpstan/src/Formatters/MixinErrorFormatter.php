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
final class MixinErrorFormatter implements ErrorFormatter
{
    public function __construct(protected TableErrorFormatter $tableErrorFormatter)
    {
    }

    public function formatErrors(AnalysisResult $result, Output $output): int
    {
        $result = $this->rewrite($result);

        return $this->tableErrorFormatter->formatErrors($result, $output);
    }

    protected function rewrite(AnalysisResult $result): AnalysisResult
    {
        /** @var list<Error> $errors */
        $errors = Arr::map($result->getFileSpecificErrors(), $this->error(...));

        /** @phpstan-ignore phpstanApi.constructor */
        return new AnalysisResult(
            $errors,
            $result->getNotFileSpecificErrors(),
            $result->getInternalErrorObjects(),
            $result->getWarnings(),
            $result->getCollectedData(),
            $result->isDefaultLevelUsed(),
            $result->getProjectConfigFile(),
            $result->isResultCacheSaved(),
            $result->getPeakMemoryUsageBytes(),
            $result->isResultCacheUsed(),
            $result->getChangedProjectExtensionFilesOutsideOfAnalysedPaths()
        );
    }

    protected function error(Error $error): Error
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
