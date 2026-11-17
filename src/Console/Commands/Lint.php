<?php

namespace Mpietrucha\Laravel\Essentials\Console\Commands;

use Mpietrucha\Laravel\Essentials\Console\Command;
use Mpietrucha\Laravel\Essentials\Console\Commands\Concerns\InteractsWithLint;
use Mpietrucha\Support\Filesystem\Cwd;

class Lint extends Command
{
    use InteractsWithLint;

    /**
     * @var string
     */
    #[\Override]
    protected $signature = 'essentials:lint
                            {path? : The path to lint}';

    /**
     * @var string
     */
    #[\Override]
    protected $description = 'Lint the application files';

    public function handle(): void
    {
        /** @var string $path */
        $path = $this->argument('path') ?? Cwd::get();

        $this->lint($path);

        $this->info('Files linted successfully.');
    }
}
