<?php

namespace TomShaw\Mediable\Console\Commands;

use Illuminate\Console\Command;
use RuntimeException;
use Symfony\Component\Process\Process;

class InstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mediable:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Installs and publishes (config, views, images) provided by Mediable.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->comment('Publishing Mediable Config...');
        $this->callSilent('vendor:publish', ['--tag' => 'mediable.config']);

        $this->comment('Publishing Mediable Views...');
        $this->callSilent('vendor:publish', ['--tag' => 'mediable.views']);

        $this->comment('Publishing Mediable Images...');
        $this->callSilent('vendor:publish', ['--tag' => 'mediable.images']);

        $this->comment('Building Mediable Assets...');
        $this->buildAssets();

        $this->info('Mediable installed successfully!');
    }

    private function buildAssets()
    {
        $process = new Process(['npm', 'run', 'build']);

        $process->setWorkingDirectory(base_path())
            ->setTimeout(null)
            ->run(function ($type, $buffer) {
                if ($type === Process::ERR) {
                    $this->error($buffer);
                } else {
                    $this->line($buffer);
                }
            });

        if (! $process->isSuccessful()) {
            throw new RuntimeException($process->getErrorOutput());
        }
    }
}
