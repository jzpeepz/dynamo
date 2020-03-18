<?php

namespace Jzpeepz\Dynamo;

use Illuminate\Support\Str;

class FormTab extends ModuleTab
{
    public $fields = null;
    private $dynamo;

    public function __construct($name, $options = [])
    {
        $this->name = $name;
        $this->key = Str::slug($name);
        $this->options = collect();
        $this->fields = collect();
        $this->dynamo = new Dynamo('');
    }

    public function __call($name, $arguments = [])
    {
        $this->dynamo = call_user_func_array([$this->dynamo, $name], $arguments);

        $field = $this->dynamo->popField();

        $this->fields->push($field);

        return $this;
    }
    
}
