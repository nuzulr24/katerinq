<?php

namespace MadWeb\Enum;

use Illuminate\Support\ServiceProvider;
use MadWeb\Enum\Console\EnumMakeCommand;

class EnumServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        // Config
        $this->publishes([
            __DIR__.'/../config/enum.php' => config_path('enum.php'),
        ], 'config');

        // Command
        if ($this->app->runningInConsole()) {
            $this->commands([
                EnumMakeCommand::class,
            ]);
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/enum.php', 'enum');
    }
}
