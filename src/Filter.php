<?php

namespace Jzpeepz\Dynamo;

use Collective\Html\FormFacade as Form;

class Filter
{
    private $key;

    private $closure;

    private $options;

    private $parameters;

    /**
     * __construct
     *
     * @param string $key
     * @param array|object $options
     * @param callable $closure
     * @param mixed array
     * @return Filter
     */
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

    /**
     * make
     *
     * @param string $key
     * @param array|object $options
     * @param callable $closure
     * @param array $parameters
     * @return Filter
     */
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
        $default = isset($this->parameters['default']) ? $this->parameters['default'] : null;
        return '<div class="form-group mb-3 mr-2"><label>' . $this->getLabel() . '</label> ' . Form::select($this->key, $this->options, request()->has($this->key) ? request()->input($this->key) : $default, $this->parameters) . '</div>';
    }

    public function getLabel()
    {
        $label = ucwords(str_replace('_', ' ', $this->key));

        if (isset($this->parameters['label'])) {
            $label = $this->parameters['label'];
        }

        return $label;
    }
}
