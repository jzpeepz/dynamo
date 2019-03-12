<?php

namespace Jzpeepz\Dynamo\Console\Commands;

use Illuminate\Console\Command;
use Jzpeepz\Dynamo\Dynamo;
use Jzpeepz\Dynamo\LaravelVersion;

class DynamoMake extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:dynamo {model} {--migration=yes} {--model=yes} {--controller=yes} {--route=yes}';

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
        $srcPath = __DIR__.'/../..';

        $model = $this->argument('model');
        $table = strtolower(snake_case(str_plural($model)));

        $makeMigration = $this->option('migration');
        $makeModel = $this->option('model');
        $makeController = $this->option('controller');
        $makeRoute = $this->option('route');

        $stubsDirectory = $srcPath.'/Console/stubs/';

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
                $this->writeModelFile($model);

    			$this->info($model . ' model successfully created.');
    		} else {
    			$this->error($model . ' model file already exists!');
    			return;
    		}
        }

        // make dynamo controller
        if ($makeController == 'yes') {
            $controllerDirectory = config('dynamo.controller_path') . '/';
            $newControllerFile = $controllerDirectory . $model . 'Controller.php';
    		if (! file_exists($newControllerFile)) {
    			// pull in controller stub
    			$controllerStub = file_get_contents($stubsDirectory . 'DynamoController.stub');

    			// replace values in stub
    			$controllerString = str_replace('$MODEL$', $model, $controllerStub);

    			$controllerString = str_replace('$NAMESPACE$', trim(config('dynamo.controller_namespace'), '\\'), $controllerString);

    			// create controller file
    			file_put_contents($newControllerFile, $controllerString);

    			$this->info($model . 'Controller successfully created.');
    		} else {
    			$this->error($model . 'Controller file already exists!');
    			return;
    		}
        }

        // insert routes
        if ($makeRoute == 'yes') {
            $resource = strtolower($model);
    		$route = "Route::resource('$resource', '" . config('dynamo.controller_namespace') . "\\{$model}Controller');";

    		// get routes file source
            $routesPath = base_path('routes/web.php');
            if (LaravelVersion::is('4.2')) {
                $routesPath = app_path('routes.php');
            } elseif (LaravelVersion::is(['5.0', '5.1', '5.2'])) {
                $routesPath = app_path('Http/routes.php');
            }
    		$source = file_get_contents($routesPath);

    		// insert new route
    		$insertMarker = '/* ---- Dynamo Routes ---- */';
            $hasMarker = strpos($source, $insertMarker);
            if ($hasMarker === false) {
                $source = $source . "\n" . $route;
            } else {
    		    $source = str_replace($insertMarker, $insertMarker . "\n    " . $route, $source);
            }

    		// rewrite routes file
    		file_put_contents($routesPath, $source);
        }

		$this->info('Complete the migration and get started by linking to:');
        $this->comment("route('" . config('dynamo.route_prefix') . strtolower($model) . ".index')");
    }

    public function getStubsDirectory()
    {
        $srcPath = __DIR__.'/../..';

        return $srcPath.'/Console/stubs/';
    }

    public function writeModelFile($model)
    {
        $modelDirectory = app_path('/');

        $newModelFile = $modelDirectory . $model . '.php';

        // pull in model stub
        $modelStub = file_get_contents($this->getStubsDirectory() . 'DynamoModel.stub');

        // replace values in stub
        $modelString = str_replace('$MODEL$', $model, $modelStub);

        // insert custom use statements
        $modelUses = config('dynamo.model_uses', []);

        $modelUses = array_map(function ($value) {
            return "use $value;";
        },$modelUses);

        $modelString = str_replace('// use statements', implode("\n", $modelUses), $modelString);

        // insert implements
        $modelImplements = config('dynamo.model_implements', []);

        if (! empty($modelImplements)) {
            $modelString = str_replace('// implements', 'implements ' . implode(', ', $modelImplements), $modelString);
        }

        // insert traits
        $modelTraits = config('dynamo.model_traits', []);

        if (! empty($modelTraits)) {
            $modelString = str_replace('// traits', 'use ' . implode(', ', $modelTraits) . ';', $modelString);
        }

        // insert custom methods
        $methodsString = view('dynamo::models.methods');
        $modelString = str_replace('// methods', $methodsString, $modelString);

        // create model file
        file_put_contents($newModelFile, $modelString);
    }
}
