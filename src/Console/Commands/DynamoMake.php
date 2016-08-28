<?php

namespace Jzpeepz\Dynamo\Console\Commands;

use Illuminate\Console\Command;
use Jzpeepz\Dynamo\Dynamo;

class DynamoMake extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:dynamo {model} {--migration=yes} {--model=yes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a Dynamo driven admin';

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
        $model = $this->argument('model');
        $table = strtolower(snake_case(str_plural($model)));

        $makeMigration = $this->option('migration');
        $makeModel = $this->option('model');

        $stubsDirectory = app_path('/Console/stubs/');

        // make migration
        if ($makeMigration == 'yes') {
            $this->call('make:migration', [
                'name' => 'create_' . $table . '_table', '--create' => $table
            ]);
        }

        // make model
        if ($makeModel == 'yes') {
            $modelDirectory = app_path('/');
            $newModelFile = $modelDirectory . $model . '.php';
    		if (! file_exists($newModelFile)) {
    			// pull in model stub
    			$modelStub = file_get_contents($stubsDirectory . 'DynamoModel.stub');

    			// replace values in stub
    			$modelString = str_replace('$MODEL$', $model, $modelStub);

    			// create model file
    			file_put_contents($newModelFile, $modelString);

    			$this->info($model . ' model successfully created.');
    		} else {
    			$this->error($model . ' model file already exists!');
    			return;
    		}
        }

        // make dynamo controller

        $controllerDirectory = app_path('/Http/Controllers/Admin/');
        $newControllerFile = $controllerDirectory . $model . 'Controller.php';
		if (! file_exists($newControllerFile)) {
			// pull in controller stub
			$controllerStub = file_get_contents($stubsDirectory . 'DynamoController.stub');

			// replace values in stub
			$controllerString = str_replace('$MODEL$', $model, $controllerStub);

			// create controller file
			file_put_contents($newControllerFile, $controllerString);

			$this->info($model . 'Controller successfully created.');
		} else {
			$this->error($model . 'Controller file already exists!');
			return;
		}

        // insert routes
        $resource = strtolower($model);
		$route = "Route::resource('$resource', '\\App\\Http\\Controllers\\Admin\\{$model}Controller');";

		// get routes file source
		$source = file_get_contents(app_path('/Http/routes.php'));

		// insert new route
		$insertMarker = '/* ---- Dynamo Routes ---- */';
		$source = str_replace($insertMarker, $insertMarker . "\n    " . $route, $source);

		// rewrite routes file
		file_put_contents(app_path('/Http/routes.php'), $source);

		$this->info('Complete the migration and get started by linking to:');
        $this->comment("route('admin." . strtolower($model) . ".index')");
    }
}
