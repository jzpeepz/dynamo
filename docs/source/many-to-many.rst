Creating Many-to-Many Relationships Between Dynamo Models
=========================================================


.. raw:: html

    <strong style="font-size: 20px;">Step 1: Generate the two models you will be using.</strong><br><br>


.. code-block:: trafficscript

    php artisan make:dynamo Faq
    php artisan make:dynamo Category


.. raw:: html

    <strong style="font-size: 20px;">Step 2: Complete the needed migrations.</strong><br><br>

Example Faq migration:

.. code-block:: trafficscript

    Schema::create('faqs', function (Blueprint $table) {
    	$table->increments('id');
    	$table->string('question', 255);
    	$table->mediumText('answer');
    	$table->timestamps();
    });

Example Category migration:

.. code-block:: trafficscript

    Schema::create('categories', function (Blueprint $table) {
    	$table->increments('id');
    	$table->string('name');
    	$table->timestamps();
    });

Example pivot table migration:

.. code-block:: trafficscript

    Schema::create('category_faq', function(Blueprint $table) {
    	$table->integer('faq_id')->unsigned()->nullable();
    	$table->foreign('faq_id')->references('id')->on('faqs');

    	$table->integer('category_id')->unsigned()->nullable();
    	$table->foreign('category_id')->references('id')->on('categories');
    });

Run:

.. code-block:: trafficscript

    php artisan migrate

.. raw:: html

    <strong style="font-size: 20px;">Step 3: Add the proper belongsToMany Eloquent function to each model.</strong><br><br>


For the Category model:

.. code-block:: trafficscript

    public function faqs()
    {
	   return $this->belongsToMany('App\Faq');
    }

For the Faq Model:

.. code-block:: trafficscript

    public function categories()
    {
	return $this->belongsToMany('App\Category');
    }


.. raw:: html

    <strong style="font-size: 20px;">Step 4: Chain the hasMany() method onto your Dynamo instance in both controllers. Make sure your key is the name of the Eloquent function from you model.</strong><br><br>

.. code-block:: trafficscript

    return Dynamo::make(\App\Employee::class)
			->hasMany('categories', ['options' => [$categories]]);

.. note:: NOTE: You can see a full example of this process in the next section, Dynamo Methods, on the hasManySimple function
