<?php

namespace Jzpeepz\Dynamo\Console\Commands;

use Illuminate\Console\Command;
use Jzpeepz\Dynamo\Dynamo;
use Jzpeepz\Dynamo\LaravelVersion;
use Illuminate\Support\Str;

class DynamoViews extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dynamo:views {model}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate views for a Dynamo admin';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $srcPath = __DIR__.'/../..';

        $model = $this->argument('model');
        $table = strtolower(Str::snake(str_plural($model)));

        // get Dynamo instance
        $controllerClass = config('dynamo.controller_namespace') . '\\' . $model . 'Controller';
        $controller = new $controllerClass;
        $dynamo = $controller->getDynamo();

        // ensure that folders for generated views exist
        $viewPathParts = explode('.', config('dynamo.view_prefix') . '.' . $table);
        $viewPath = base_path('resources/views');
        foreach ($viewPathParts as $viewPathPart) {
            $viewPath .= '/' . $viewPathPart;
            if (! \File::isDirectory($viewPath)) {
                \File::makeDirectory($viewPath);
            }
        }

        // generate the index view
        $viewSrc = view('dynamo::stubs.index', compact('dynamo'))->render();
        $viewDestination = str_replace('.', '/', config('dynamo.view_prefix')) . '/' . $table . '/index.blade.php';
        if (file_exists(base_path('resources/views/' . $viewDestination))) {
            $this->error('Index view for ' . $table . ' already exists.');
        } else {
            file_put_contents(base_path('resources/views/' . $viewDestination), $viewSrc);
            $this->info('Index view for ' . $table . ' created.');
        }

        // generate the form view
        $viewSrc = view('dynamo::stubs.form', compact('dynamo'))->render();
        $viewDestination = str_replace('.', '/', config('dynamo.view_prefix')) . '/' . $table . '/form.blade.php';
        if (file_exists(base_path('resources/views/' . $viewDestination))) {
            $this->error('Form view for ' . $table . ' already exists.');
        } else {
            file_put_contents(base_path('resources/views/' . $viewDestination), $viewSrc);
            $this->info('Form view for ' . $table . ' created.');
        }
    }
}
