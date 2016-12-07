<?php

namespace Jzpeepz\Dynamo;

class DynamoField
{
    private $attributes = [];

    public function __construct($attributes = [])
    {
        $this->attributes = $attributes;
    }

    public function __get($name)
    {
        return isset($this->attributes[$name]) ? $this->attributes[$name] : null;
    }

    public static function make($attributes = [])
    {
        return new DynamoField($attributes);
    }

    public function toArray()
    {
        return $this->attributes;
    }

    public function render($item)
    {
        if($this->render){
            return view('dynamo::partials.fields.' . $this->type, ['field' => $this, 'item' => $item])->render();
        }

    }

    public function getOption($key)
    {
        return isset($this->options[$key]) ? $this->options[$key] : '';
    }

    // public function renderText($item)
    // {
    //     return view('admin.dynamo.partials.fields.text', ['field' => $this, 'item' => $item])->render();
    // }
    //
    // public function renderPassword($item)
    // {
    //     return view('admin.dynamo.partials.fields.password', ['field' => $this, 'item' => $item])->render();
    // }
    //
    // public function renderSelect($item)
    // {
    //     return view('admin.dynamo.partials.fields.select', ['field' => $this, 'item' => $item])->render();
    // }
    //
    // public function renderFile($item)
    // {
    //     return view('admin.dynamo.partials.fields.file', ['field' => $this, 'item' => $item])->render();
    // }
}
