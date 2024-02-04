<?php

namespace TomShaw\Mediable\Console\Commands;

use Illuminate\Console\Command;

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

        $this->info('Mediable installed successfully!');
    }
}
