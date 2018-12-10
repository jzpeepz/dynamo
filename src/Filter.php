<?php

namespace Jzpeepz\Dynamo;

use Collective\Html\FormFacade as Form;

class Filter {

    private $key;

    private $closure;

    private $options;

    private $parameters;

    public function __construct($key, $options, $closure, $parameters = [])
    {
        $this->key = $key;

        $this->options = $options;

        $this->closure = $closure;

        $defaults = [
            'class' => 'form-control',
        ];

        $this->parameters = array_merge($defaults, $parameters);
    }

    public static function make($key, $options, $closure, $parameters = [])
    {
        return new static($key, $options, $closure, $parameters);
    }

    public function __toString()
    {
        return $this->render();
    }

    public function modifyQuery($query)
    {
        return call_user_func($this->closure, $query);
    }

    public function render()
    {
        return '<div class="form-group mb-3 mr-2"><label>' . ucwords(str_replace('_', ' ', $this->key)) . '</label> ' . Form::select($this->key, $this->options, request()->has($this->key) ? request()->input($this->key) : null, $this->parameters) . '</div>';
    }

}
