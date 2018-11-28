The Dynamo Controller
=====================

The most helpful part about Dynamo is the Dynamo Controller. We highly encourage you to take a look at the DynamoController class and read the comments
of each function to have a better understanding of what's going on; however you don't really need to understand anything that's going on in this section
to be able to use Dynamo; in fact the whole point of this package is so you don't have to do this stuff yourself. If you'd like, skip this section, but its good
to know whats going on under the hood ;) .

.. image:: https://media.giphy.com/media/6PAbFX7jVXWTK/giphy.gif
   :align: center
   :height: 400
   :width: 600

Starting off, the Dynamo Controller has an index function that returns an index view of all the resources. So in the Employee
example, the index function would show a view of all the Employee models saved in the database::

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $items = $this->dynamo->getIndexItems();

        return DynamoView::make($this->dynamo, 'dynamo::index', compact('items'));
    }

The next two functions are create() and store(). Create shows the form view that the user will use to create Employee objects::

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $item = new $this->dynamo->class;

        $formOptions = [
            'route' => $this->dynamo->getRoute('store'),
            'files' => true,
        ];

        return DynamoView::make($this->dynamo, 'dynamo::form', compact('item', 'formOptions'));
    }

Store() is the function that gets hit when the user presses the submit button on the Create an Employee form. Store will "store" this new Employee object in your database::

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $item = new $this->dynamo->class;

        $this->dynamo->store($item);

        session(['alert-success' => $this->dynamo->getName() . ' was saved successfully!']);

        return redirect()->route($this->dynamo->getRoute('edit'), $item->id);
    }

The next two functions are edit() and update() which go hand-in-hand the same way create() and store() go hand-in-hand. When the user clicks the edit button on one of
the Employee objects in the index view, the form view for that particular employee will be presented to the user so they can make changes to that Employee
(perhaps update their phone number)::

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $className = $this->dynamo->class;

        $item = $className::withoutGlobalScopes()->find($id);

        $formOptions = [
            'route' => [$this->dynamo->getRoute('update'), $id],
            'method' => 'put',
            'files' => true,
        ];

        return DynamoView::make($this->dynamo, 'dynamo::form', compact('item', 'formOptions'));
    }

Update() gets hit when the user presses the Submit button and whatever changes they made will get updated for that particular Employee in the database::

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $className = $this->dynamo->class;

        $item = $className::withoutGlobalScopes()->find($id);

        $this->dynamo->store($item);

        session(['alert-success' => $this->dynamo->getName() . ' was saved successfully!']);

        return redirect()->route($this->dynamo->getRoute('edit'), $id);
    }

The final function on the Dynamo Controller is destroy(). This function gets hit when the user clicks the delete button in the index view, and an alert will appear asking
them if they are sure they want to do this. If they press yes, the item will attempt to be deleted. If the item can't be deleted due to throwing a QueryException (because
you can't add or update a child row if another object in the database is using it for a foreign key), it will redirect and say you can't do that because this object is in use.
Otherwise, the item will be deleted and they will be shown a success message, and this Employee no longer exist::

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        $className = $this->dynamo->class;

        // Run through and look for fields with type 'multiSelect'
        foreach($this->dynamo->getFields() as $field) {

            if($field->type == 'hasMany') {
                //if 'multiSelect' found then relational data may exist. Detach data from the model
                $className::withoutGlobalScopes()->find($id)->{$field->key}()->detach();
            }

        }

        $item = $className::withoutGlobalScopes()->findOrFail($id);

        try {
            $item->delete();
        } catch (QueryException $e) {
            session(['alert-danger' => $this->dynamo->getName() . ' cannot be deleted while in use!']);
            return redirect()->route($this->dynamo->getRoute('index'));
        }

        session(['alert-warning' => $this->dynamo->getName() . ' was deleted successfully!']);

        return redirect()->route($this->dynamo->getRoute('index'));
    }
