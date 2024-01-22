<?php

namespace TomShaw\Mediable\Tests\Support;

use Illuminate\Support\Facades\View;
use Livewire\Livewire;
use Livewire\LivewireServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;
use TomShaw\Mediable\MediaBrowser;
use TomShaw\Mediable\Providers\MediableServiceProvider;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('app.key', '6rE9Nz59bGRbeMATftriyQjrpF7DcOQm');

        //View::addNamespace('test', __DIR__ . '/resources/views');

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
