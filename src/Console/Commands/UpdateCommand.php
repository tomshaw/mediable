<?php

namespace TomShaw\Mediable\Console\Commands;

use Illuminate\Console\Command;

class UpdateCommand extends Command
{
    use BuildsAssets;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mediable:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updates and publishes (config, views, images) provided by Mediable.';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->info('This will overwrite Mediable (config, views, images, fonts).');

        if ($this->confirm('Do you wish to continue?', true)) {
            $this->comment('Updating Mediable Config...');
            $this->callSilent('vendor:publish', ['--tag' => 'mediable.config', '--force' => true]);

            $this->comment('Updating Mediable Assets...');
            $this->callSilent('vendor:publish', ['--tag' => 'mediable.views', '--force' => true]);

            $this->comment('Updating Mediable Images...');
            $this->callSilent('vendor:publish', ['--tag' => 'mediable.images', '--force' => true]);

            $this->comment('Updating Mediable Fonts...');
            $this->callSilent('vendor:publish', ['--tag' => 'mediable.fonts', '--force' => true]);

            $this->comment('Building Mediable Assets...');
            $this->buildAssets();
        }

        $this->info('Mediable updated successfully!');
    }
}
