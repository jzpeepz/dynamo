Creating Many-to-Many Relationships Between Dynamo Models
=========================================================

Step 1: Generate the two models you will be using.
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

.. code-block:: PHP

    php artisan make:dynamo Faq
    php artisan make:dynamo Category

Step 2: Complete the needed migrations.
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

Example Faq migration::

    Schema::create('faqs', function (Blueprint $table) {
    	$table->increments('id');
    	$table->string('question', 255);
    	$table->mediumText('answer');
    	$table->timestamps();
    });

Example Category migration::

    Schema::create('categories', function (Blueprint $table) {
    	$table->increments('id');
    	$table->string('name');
    	$table->timestamps();
    });

Example pivot table migration::

    Schema::create('category_faq', function(Blueprint $table) {
    	$table->integer('faq_id')->unsigned()->nullable();
    	$table->foreign('faq_id')->references('id')->on('faqs');

    	$table->integer('category_id')->unsigned()->nullable();
    	$table->foreign('category_id')->references('id')->on('categories');
    });

Run::

    php artisan migrate

Step 3: Add the proper belongsToMany Eloquent function to each model.
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

For the Category model::

    public function faqs()
    {
	   return $this->belongsToMany('App\Faq');
    }

For the Faq Model::

    public function categories()
    {
	return $this->belongsToMany('App\Category');
    }

Step 4: Chain the hasMany() method onto your Dynamo instance in both controllers. Make sure your key is the name of the Eloquent function from you model.
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
.. code-block:: PHP

    return Dynamo::make(\App\Employee::class)
			->hasMany('categories', ['options' => [$categories]]);
