## Installation

Install via Composer:

`composer require jzpeepz/dynamo`

Include the service provider in your `config/app.php`:

`Jzpeepz\Dynamo\DynamoServiceProvider::class`

Publish the Dynamo config file:

`php artisan vendor:publish --tag=dynamo`

NOTE: If using a local disk for uploading, be sure to symlink it to your public directory and provide the proper path in the config file.

## Configuration

`storage_disk` Storage disk to use to store uploaded files. Default: 'local'

`upload_path` Path within the storage disk to store the uploaded files. This is also the directory within the public directory to which the storage directory is linked. Default: '/uploads/'

`route_prefix` Prefix to add to all Dynamo routes. Default: '' (empty string)

`layout` Layout to use with Dynamo views. Default: 'layouts.app'

`controller_namespace` Namespace for generated controllers. Default:  'App\Http\Controllers'

`controller_path` Path for storing generated controllers. Default: app_path('/Http/Controllers')

`view_prefix` Prefix for overridden views. Default: 'dynamo'

`view_theme` Theme used for views. Default: 'bootstrap4'

`target_blade_section` The blade section in templates where views are rendered. Default: 'content'

`default_has_many_class` CSS class used by default on hasMany field types. Default: ''

`model_uses` This value contains an array of the classes that should be imported into the generated model class. Default: []

`model_implements` This value contains an array of the interfaces that should be implemented by the generated model class. Default: []

`model_traits` This value contains an array of the traits that should be used by the generated model class. Default: []

## Usage

### Generating your first admin

The following command will create a controller, model, migration, and route for your admin:

`php artisan make:dynamo Employee`

Need to opt out of some of the Dynamo magic?

`php artisan make:dynamo Employee --migration=no --model=no --controller=no --route=no`

### Customizing the admin

Admin customization happens in your controller inside the `getDynamo()` function. This function returns a Dynamo instance which has lots of chainable methods that customize your Dynamo admin. Below are methods you can chain.

## Customizing the index

By default, Dynamo will add all fields from the database table to the index. Removing the call to `auto()` in the `getDynamo()` method in the generated controller, will stop all fields from getting added to the index AND the form.

### Adding index columns

**addIndex($key, $label = null, $formatCallable = null)**

This method allows you to add or update an index column.

**Parameters:**

`$key` This is the column name in your database table if you are hoping to populate it with table data. Otherwise, it could be any unique name.

`$label` (optional) This is pretty name you want folks to see.

`$formatCallable` (optional) This is a closure that allows you to completely customize how the index column renders. This closure will receive one parameter of the Eloquent instance for that row. The closure should return what you would like to render in the index column.

### Removing index columns

**removeIndex($key)**

This method allow you to remove a column from the index.

**Parameters:**

`$key` This is the key used to create the index. It is typical the column name in the database table.

### Removing all index columns

**clearIndexes()**

This method removes all index columns. This can be used to clear indexes create by `auto()` while leaving the form fields in place.

### Sorting index rows

**indexOrderBy($column, $sort = 'asc')**

This method allows you to order the rows returned from the database.

### Paginate the index rows

**paginate($limit)**

This method allows you to set the number of rows to display on each page of the index.

### Create tabs on the index

### Add search to the index

### Add filters to the index

### Hide the delete button on each row

### Hide the add button

### Add buttons to the index

### Add action buttons to the index


## Customizing the form

### Add a form field

### Remove a form field
				
### Creating form groups

	return Dynamo::make(\App\Employee::class)
			->group(FieldGroup::make('groupName')
			    ->text('fieldName')
				->text('fieldName');
			});

### Creating form tabs

### Add a custom handler for a field

## Extending Dynamo

### Creating custom fields

### Creating handlers to custom fields

## Advanced Dynamo

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

## Check out our more detailed Documentation

https://dynamo-admin.readthedocs.io/en/latest/index.html

## License

Dynamo is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT).
