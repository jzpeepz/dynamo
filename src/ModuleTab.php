<?php

namespace Jzpeepz\Dynamo;

class ModuleTab
{
    protected $name;
    public $key;
    protected $options;

    public function __construct($name)
    {
        $this->name = $name;
        $this->key = str_slug($name);
        $this->options = collect();
    }

    public static function make($name)
    {
        return new static($name);
    }

    public function getName()
    {
        return $this->name;
    }

    public function getOptions()
    {
        return $this->options;
    }

    public function hasOption($key)
    {
        return isset($this->options[$key]);
    }

    public function getOption($key)
    {
        return $this->hasOption($key) ? $this->options[$key] : null;
    }

    public function tooltip($text)
    {
        $this->options->put('tooltip', $text);

        return $this;
    }
}
