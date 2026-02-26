<?php

namespace Mpietrucha\Laravel\Essentials\Commands;

use Illuminate\Console\Command;
use Mpietrucha\Laravel\Essentials\Commands\Concerns\InteractsWithLint;
use Mpietrucha\Utility\Filesystem\Cwd;

class Lint extends Command
{
    use InteractsWithLint;

    /**
     * @var string
     */
    protected $signature = 'essentials:lint
                            {path? : The path to lint}';

    /**
     * @var string
     */
    protected $description = 'Lint the application files';

    public function handle(): void
    {
        $path = $this->argument('path') ?? Cwd::get();

        $this->lint($path);

        $this->info('Files linted successfully');
    }
}
