<?php

namespace Updev\FileHandler\Providers;

use Updev\FileHandler\FileHandler;
use Illuminate\Support\ServiceProvider;

class FileHandlerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('FileHandler', function () {
            return new FileHandler;
        });
    }
}
