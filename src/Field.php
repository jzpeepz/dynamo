<?php

namespace Jzpeepz\Dynamo;

class Field {

    private $attributes = [];

    public function __construct($name)
    {
        $this->name = $name;
        $this->type = strtolower(class_basename(get_class($this)));
    }

    public function __toString()
    {
        return view('dynamo::partials.fields.' . $this->type, ['field' => $this])->render();
    }

    public static function make($name)
    {
        return new static($name);
    }

    public function __get($name)
    {
        return isset($this->attributes[$name]) ? $this->attributes[$name] : null;
    }

    public function __set($name, $value)
    {
        $this->attributes[$name] = $value;
    }

    public function getAttribute($name)
    {
        return isset($this->attributes[$name]) ? $this->attributes[$name] : null;
    }

    /**
     * Deprecated
     */
    public function getOption($name)
    {
        return $this->getAttribute($name);
    }
}
