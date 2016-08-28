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
        //
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
