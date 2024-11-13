<?php

namespace Jaldi\SetupPackage;

use Illuminate\Support\ServiceProvider;

class SetupPackageServiceProvider extends ServiceProvider
{
    public function register()
    {
        // Merge default config with published config
        $this->mergeConfigFrom(__DIR__ . '/../config/setup.php', 'setup');
    }

    public function boot()
    {
        // Publish config file to the main Laravel config directory
        $this->publishes([
            __DIR__ . '/../config/setup.php' => config_path('setup.php'),
        ], 'config');

        // Register your command
        if ($this->app->runningInConsole()) {
            $this->commands([
                Commands\SetupCommand::class,
            ]);
        }
    }
}
