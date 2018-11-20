<?php

namespace Jzpeepz\Dynamo;

use Collective\Html\FormFacade as Form;

class Filter {

    private $key;

    private $closure;

    private $options;

    public function __construct($key, $options, $closure)
    {
        $this->key = $key;

        $this->options = $options;

        $this->closure = $closure;
    }

    public static function make($key, $options, $closure)
    {
        return new static($key, $options, $closure);
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
        return '<div class="form-group"><label>' . ucwords(str_replace('_', ' ', $this->key)) . '</label> ' . Form::select($this->key, $this->options, null, ['class' => 'form-control']) . '</div>';
    }

}
