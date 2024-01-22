<?php

namespace TomShaw\Mediable\Console\Commands;

use Illuminate\Console\Command;

class PackageInstall extends Command
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
    protected $description = 'A command to install the Mediable package assets.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->call('vendor:publish', [
            '--provider' => 'TomShaw\Mediable\Providers\MediableServiceProvider',
            '--tag' => 'config'
        ]);
    
        $this->call('vendor:publish', [
            '--provider' => 'TomShaw\Mediable\Providers\MediableServiceProvider',
            '--tag' => 'views'
        ]);
    
        $this->call('vendor:publish', [
            '--provider' => 'TomShaw\Mediable\Providers\MediableServiceProvider',
            '--tag' => 'images'
        ]);
    
        $this->info('Mediable assets successfully installed!');
    }
}
