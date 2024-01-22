<?php

namespace TomShaw\Mediable\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use TomShaw\Mediable\Assets\Scripts;
use TomShaw\Mediable\Assets\Styles;
use TomShaw\Mediable\Components\MediaBrowser;
use TomShaw\Mediable\Console\Commands\PackageInstall;

class MediableServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../../resources/views', 'mediable');
        $this->loadViewsFrom(__DIR__.'/../../resources/icons', 'icons');

        $this->loadMigrationsFrom(__DIR__.'/../../resources/database/migrations');

        Livewire::component('mediable', MediaBrowser::class);

        Blade::component('mediable::scripts', Scripts::class);
        Blade::component('mediable::styles', Styles::class);

        Blade::component('icons::attachments', 'icons.attachments');
        Blade::component('icons::tables', 'icons.tables');
        Blade::component('icons::square', 'icons.square');
        Blade::component('icons::plus', 'icons.plus');
        Blade::component('icons::minus', 'icons.minus');
        Blade::component('icons::logo', 'icons.logo');
        Blade::component('icons::exit', 'icons.exit');
        Blade::component('icons::expand', 'icons.expand');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../../resources/config/config.php' => config_path('mediable.php'),
            ], 'config');
            $this->publishes([
                __DIR__.'/../../resources/views' => resource_path('views/vendor/mediable'),
            ], 'views');
            $this->publishes([
                __DIR__.'/../../resources/images' => public_path('vendor/mediable/images'),
            ], 'images');
        }

        Blade::directive('mediableStyles', function () {
            return "<?php echo view('mediable::assets.styles')->render(); ?>";
        });

        Blade::directive('mediableScripts', function () {
            return "<?php echo view('mediable::assets.scripts')->render(); ?>";
        });
    }

    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../../resources/config/config.php', 'mediable');

        $this->commands([
            PackageInstall::class,
        ]);
    }
}
