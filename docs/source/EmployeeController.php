<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Jzpeepz\Dynamo\Dynamo;
use Jzpeepz\Dynamo\Http\Controllers\DynamoController;
use App\Employee;

class EmployeeController extends DynamoController
{
    public function getDynamo()
    {
        return Dynamo::make(\App\Employee::class)
                    ->auto()
                    ->text('first_name')
                    ->text('last_name')
                    ->file('photo', 'Headshot')
                    ->textarea('bio')

                    //ClearIndexes
                    ->clearIndexes()
                    ->addIndex('first_name')
                    ->addIndex('last_name')
                    ->addIndex('photo', 'Headshot', function($item) {
                        if(empty($item->photo)) {
                            return ''
                        }
                        return '<img style="width: 100px  " src="'.$item->photo.'" class="" style="width: 60px;">';
                    })
                    ->indexOrderBy('last_name');

    }
