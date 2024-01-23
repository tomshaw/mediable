<?php

namespace TomShaw\Mediable\Tests\Support;

use Livewire\Livewire;
use Livewire\LivewireServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;
use TomShaw\Mediable\Components\MediaBrowser;
use TomShaw\Mediable\Providers\MediableServiceProvider;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        $randomKey = base64_encode(random_bytes(32));

        config()->set('app.key', $randomKey);

        $this->registerLivewireComponents();
    }

    protected function getPackageProviders($app)
    {
        return [
            LivewireServiceProvider::class,
            MediableServiceProvider::class,
        ];
    }

    private function registerLivewireComponents(): self
    {
        Livewire::component('mediable', MediaBrowser::class);

        return $this;
    }
}
