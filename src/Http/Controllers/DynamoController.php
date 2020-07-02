<?php

namespace Jzpeepz\Dynamo\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Jzpeepz\Dynamo\DynamoView;
use Illuminate\Database\QueryException;

class DynamoController extends Controller
{
    public function __construct()
    {
        $dynamo = $this->getDynamo();
        view()->share('dynamo', $dynamo);
        $this->dynamo = $dynamo;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $items = $this->dynamo->getIndexItems();

        if (function_exists('mimic')) {
            mimic($this->dynamo->getName() . ' Manager');
        }

        return DynamoView::make($this->dynamo, 'dynamo::index', compact('items'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $item = new $this->dynamo->class;

        if (function_exists('mimic')) {
            mimic('Create ' . $this->dynamo->getName());
        }

        $formOptions = [
            'route' => $this->dynamo->getRoute('store'),
            'files' => true,
            'autocomplete' => 'off',
            'class' => 'mb-0',
        ];

        return DynamoView::make($this->dynamo, 'dynamo::form', compact('item', 'formOptions'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $item = new $this->dynamo->class;

        if (method_exists($item, 'validator')) {
            $validator = $item->validator($request->all());

            if ($validator->fails()) {
                return back()
                        ->withErrors($validator)
                        ->withInput();
            }
        }

        $this->dynamo->store($item);

        session(['alert-success' => $this->dynamo->getName() . ' was saved successfully!']);

        return redirect()->route($this->dynamo->getRoute('edit'), $item->id);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $className = $this->dynamo->class;

        $item = $className::withoutGlobalScopes()->findOrFail($id);

        $this->dynamo = $this->dynamo->getModified($item);

        if (function_exists('mimic')) {
            mimic('Edit ' . $this->dynamo->getName());
        }

        $formOptions = [
            'route' => [$this->dynamo->getRoute('update'), $id],
            'method' => 'put',
            'files' => true,
            'autocomplete' => 'off',
            'class' => 'mb-0',
        ];

        return DynamoView::make($this->dynamo, 'dynamo::form', compact('item', 'formOptions'));
    }

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

        if (method_exists($item, 'validator')) {
            $validator = $item->validator($request->all());

            if ($validator->fails()) {
                return back()
                        ->withErrors($validator)
                        ->withInput();
            }
        }

        $this->dynamo->store($item);

        session(['alert-success' => $this->dynamo->getName() . ' was saved successfully!']);

        return redirect()->route($this->dynamo->getRoute('edit'), $id);
    }

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
        foreach ($this->dynamo->getFields() as $field) {
            if ($field->type == 'hasMany') {
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

        return $this->dynamo->getRouteUrl('index');
    }

    public function reorder(Request $request)
    {
        $ids = $request->ids;
        $data = [];
        $position = 0;
        $section = 0;

        foreach ($ids as $index => $id) {
            if ($id == '---') {
                $section++;
                $position = ($section * 100);
            } else {
                $data[$id] = $position;
                $position++;
            }
        }

        $dynamo = $this->getDynamo();
        $className = $dynamo->class;
        foreach ($data as $id => $position) {
            $className::where('id', $id)->update(['position' => $position]);
        }

        return $data;
    }
}
