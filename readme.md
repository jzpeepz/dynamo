## Installation

Install via Composer:

`composer require jzpeepz/dynamo`

Include the service provider in your `config/app.php`:

`Jzpeepz\Dynamo\DynamoServiceProvider::class`

Publish the Dynamo config file:

`php artisan vendor:publish --tag=dynamo`

NOTE: If using a local disk for uploading, be sure to symlink it to your public directory and provide the proper path in the config file.

## Configuration

`storage_disk` Storage disk to use to store uploaded files. Default: local

`upload_path` Path within the storage disk to store the uploaded files. This is also the directory within the public directory to which the storage directory is linked. Default: /uploads/

`route_prefix` Prefix to add to all Dynamo routes. Default is empty.

`layout` Layout to use with Dynamo views. Default: layouts.app

## Usage

### Generating your first admin

The following command will create a controller, model, migration, and route for your admin:

`php artisan make:dynamo Employee`

Need to opt out of some of the Dynamo magic?

`php artisan make:dynamo Employee --migration=no --model=no --controller=no --route=no`

### Customizing the admin

Admin customization happens in your controller inside the `getDynamo()` function. This function returns a Dynamo instance which has lots of chainable methods that customize your Dynamo admin.

### Creating form groups

	return Dynamo::make(\App\Employee::class)
			->group('groupName', function($dynamo) {
				$dynamo->text('fieldName')
			   		   ->text('fieldName');
			});

## License

Dynamo is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT).
