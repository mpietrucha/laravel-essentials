<?php

declare(strict_types=1);

namespace Mpietrucha\PHPStan\Formatters;

use Mpietrucha\Utility\Arr;
use PHPStan\Analyser\Error;
use PHPStan\Command\AnalysisResult;
use PHPStan\Command\ErrorFormatter\ErrorFormatter;
use PHPStan\Command\Output;

/**
 * @internal
 */
final class MixinErrorFormatter implements ErrorFormatter
{
    public function __construct(protected ErrorFormatter $table)
    {
    }

    public function formatErrors(AnalysisResult $result, Output $output): int
    {
        $result = $this->rewrite($result);

        return $this->table()->formatErrors($result, $output);
    }

    protected function rewrite(AnalysisResult $result): AnalysisResult
    {
        /** @var list<\PHPStan\Analyser\Error> $errors */
        $errors = Arr::map($result->getFileSpecificErrors(), $this->error(...));

        /** @phpstan-ignore-next-line phpstanApi.constructor */
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
        $message = $error->getMessage();

        /** @phpstan-ignore-next-line phpstanApi.constructor */
        return new Error(
            $message,
            $error->getFile(),
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

    protected function table(): ErrorFormatter
    {
        return $this->table;
    }
}
