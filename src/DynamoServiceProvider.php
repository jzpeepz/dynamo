<?php

namespace Jzpeepz\Dynamo;

use Illuminate\Support\ServiceProvider;

class DynamoServiceProvider extends ServiceProvider
{
    protected $commands = [
        'Jzpeepz\Dynamo\Console\Commands\DynamoMake',
    ];

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/resources/views', 'dynamo');

        $this->publishes([
            __DIR__.'/config/dynamo.php' => config_path('dynamo.php'),
        ], 'dynamo');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->commands($this->commands);
    }
}
