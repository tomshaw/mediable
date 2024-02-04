<?php

namespace TomShaw\Mediable\Tests\Support;

use Illuminate\Support\Str;
use Livewire\{Livewire, LivewireServiceProvider};
use Orchestra\Testbench\TestCase as Orchestra;
use TomShaw\Mediable\Components\MediaBrowser;
use TomShaw\Mediable\Providers\MediableServiceProvider;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('app.key', 'base64:'.base64_encode(Str::random(32)));

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
