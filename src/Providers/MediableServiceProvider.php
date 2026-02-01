<?php

namespace TomShaw\Mediable\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use TomShaw\Mediable\Assets\{Scripts, Styles};
use TomShaw\Mediable\Components\MediaBrowser;
use TomShaw\Mediable\Console\Commands\{InstallCommand, UpdateCommand};

class MediableServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     */
    public function boot(): void
    {
        $this->loadViews();
        $this->loadMigrations();
        $this->registerLivewireComponents();
        $this->registerBladeComponents();
        $this->registerPublishableResources();
        $this->registerBladeDirectives();
    }

    /**
     * Register bindings in the container.
     */
    public function register(): void
    {
        $this->mergeConfig();
        $this->registerCommands();
    }

    /**
     * Load views.
     */
    protected function loadViews(): void
    {
        $this->loadViewsFrom(__DIR__.'/../../resources/views', 'mediable');
        $this->loadViewsFrom(__DIR__.'/../../resources/icons', 'icons');
    }

    /**
     * Load migrations.
     */
    protected function loadMigrations(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../../resources/database/migrations');
    }

    /**
     * Register Livewire components.
     */
    protected function registerLivewireComponents(): void
    {
        Livewire::component('mediable', MediaBrowser::class);

        // Register Single File Components
        $sfcPath = __DIR__.'/../../resources/views/livewire/components';

        Livewire::addComponent('mediable-header', viewPath: $sfcPath.'/header.blade.php');
        Livewire::addComponent('mediable-toolbar', viewPath: $sfcPath.'/toolbar.blade.php');
        Livewire::addComponent('mediable-attachments', viewPath: $sfcPath.'/attachments.blade.php');
        Livewire::addComponent('mediable-preview', viewPath: $sfcPath.'/preview.blade.php');
        Livewire::addComponent('mediable-editor', viewPath: $sfcPath.'/editor.blade.php');
        Livewire::addComponent('mediable-uploads', viewPath: $sfcPath.'/uploads.blade.php');
        Livewire::addComponent('mediable-form', viewPath: $sfcPath.'/form.blade.php');
        Livewire::addComponent('mediable-sidebar', viewPath: $sfcPath.'/sidebar.blade.php');
        Livewire::addComponent('mediable-strip', viewPath: $sfcPath.'/strip.blade.php');
        Livewire::addComponent('mediable-footer', viewPath: $sfcPath.'/footer.blade.php');
    }

    /**
     * Register Blade components.
     */
    protected function registerBladeComponents(): void
    {
        Blade::component('mediable::scripts', Scripts::class);
        Blade::component('mediable::styles', Styles::class);

        Blade::component('icons::arrow', 'icons.arrow');
        Blade::component('icons::close', 'icons.close');
        Blade::component('icons::exit', 'icons.exit');
        Blade::component('icons::logo', 'icons.logo');
    }

    /**
     * Register publishable resources.
     */
    protected function registerPublishableResources(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../../resources/config/config.php' => config_path('mediable.php'),
            ], 'mediable.config');
            $this->publishes([
                __DIR__.'/../../resources/views' => resource_path('views/vendor/mediable'),
            ], 'mediable.views');
            $this->publishes([
                __DIR__.'/../../resources/images' => public_path('vendor/mediable/images'),
            ], 'mediable.images');
            $this->publishes([
                __DIR__.'/../resources/fonts' => public_path('vendor/mediable/fonts'),
            ], 'mediable.fonts');
        }
    }

    /**
     * Register Blade directives.
     */
    protected function registerBladeDirectives(): void
    {
        Blade::directive('mediableStyles', function () {
            return "<?php echo view('mediable::assets.styles')->render(); ?>";
        });

        Blade::directive('mediableScripts', function () {
            return "<?php echo view('mediable::assets.scripts')->render(); ?>";
        });
    }

    /**
     * Merge configuration.
     */
    protected function mergeConfig(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../../resources/config/config.php', 'mediable');
    }

    /**
     * Register console commands.
     */
    protected function registerCommands(): void
    {
        $this->commands([
            InstallCommand::class,
            UpdateCommand::class,
        ]);
    }
}
