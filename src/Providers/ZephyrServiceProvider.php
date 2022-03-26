<?php

namespace Legodion\Zephyr\Providers;

use Illuminate\Support\ServiceProvider;
use Legodion\Zephyr\Commands\InstallCommand;

class ZephyrServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                InstallCommand::class,
            ]);
        }
    }
}
