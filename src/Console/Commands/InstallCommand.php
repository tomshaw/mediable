<?php

namespace TomShaw\Mediable\Console\Commands;

use Illuminate\Console\Command;

class InstallCommand extends Command
{
    use BuildsAssets;

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
    protected $description = 'Installs and publishes (config, views, images, fonts) provided by Mediable.';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->comment('Publishing Mediable Config...');
        $this->callSilent('vendor:publish', ['--tag' => 'mediable.config']);

        $this->comment('Publishing Mediable Views...');
        $this->callSilent('vendor:publish', ['--tag' => 'mediable.views']);

        $this->comment('Publishing Mediable Images...');
        $this->callSilent('vendor:publish', ['--tag' => 'mediable.images']);

        $this->comment('Publishing Mediable Fonts...');
        $this->callSilent('vendor:publish', ['--tag' => 'mediable.fonts']);

        $this->comment('Building Mediable Assets...');
        $this->buildAssets();

        $this->info('Mediable installed successfully!');
    }
}
