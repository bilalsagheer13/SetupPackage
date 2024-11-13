<?php

namespace Bilalsagheer13\SetupPackage;

use Illuminate\Support\ServiceProvider;
use Bilalsagheer13\SetupPackage\Commands\SetupCommand;

class SetupPackageServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/config/setup.php' => config_path('setup.php'),
            ], 'config');

            $this->commands([
                SetupCommand::class,
            ]);
        }
    }

    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/config/setup.php', 'setup'
        );
    }
}
