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

    public function renderStub()
    {
        if($this->render){
            return view('dynamo::stubs.partials.fields.' . $this->type, ['field' => $this])->render();
        }
    }

    public function getOption($key)
    {
        return isset($this->options[$key]) ? $this->options[$key] : '';
    }

    public function getValue($item)
    {
        $key = $this->key;

        // check to see if the key ends with '_id' meaning a refence to another model
        $lastThree = substr($key, strlen($key)-3);
        if ($lastThree == '_id') {
            $class = '\\App\\'.studly_case(str_replace($lastThree, '', $key));
            $model = $class::find($item->$key);
            return $model;
        }

        // check to see if the field has a callable for formatting
        if (is_callable($this->formatCallable)) {
            return call_user_func($this->formatCallable, $item->$key);
        }

        return $item->$key;
    }

    public function getSelectOptions()
    {
        $options = collect($this->options['options']);

        if (isset($this->options['includeEmpty']) && $this->options['includeEmpty'] == true) {
            $options->prepend('', '');
        }

        return $options->toArray();
    }
}
