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

### Creating many to many relationships between dynamo models

**Step 1: Generate the two models you will be using.**
	
	php artisan make:dynamo Faq
	php artisan make:dynamo Category

**Step 2: Complete the needed migrations.**

Example Faq migration:

	Schema::create('faqs', function (Blueprint $table) {
		$table->increments('id');
		$table->string('question', 255);
		$table->mediumText('answer');
		$table->timestamps();
	});
	
Example Category migration:

	Schema::create('categories', function (Blueprint $table) {
		$table->increments('id');
		$table->string('name');
		$table->timestamps();
	});
	
Example pivot table migration:

	Schema::create('category_faq', function(Blueprint $table)
	{
		$table->integer('faq_id')->unsigned()->nullable();
		$table->foreign('faq_id')->references('id')->on('faqs');

		$table->integer('category_id')->unsigned()->nullable();
		$table->foreign('category_id')->references('id')->on('categories');
	});
	
Run `php artisan migrate`.

**Step 3: Add the proper belongsToMany Eloquent function to each model.**

For the Category model:

	public function faqs()
	{
		return $this->belongsToMany('App\Faq');
	}
	
For the Faq Model:

	public function categories()
	{
		return $this->belongsToMany('App\Category');
	}

**Step 4: Chain the `hasMany()` method onto your Dynamo instance in both controllers. Make sure your key is the name of the Eloquent function from you model.**

	return Dynamo::make(\App\Employee::class)
				->hasMany('categories', ['options' => [$categories]]);

## License

Dynamo is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT).
