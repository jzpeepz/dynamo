Creating Form Groups
====================

.. code-block:: PHP

    return Dynamo::make(\App\Employee::class)
		->group('groupName', function($dynamo) {
			$dynamo->text('fieldName')
		   		   ->text('fieldName');
		});
