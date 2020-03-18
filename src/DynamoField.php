<?php

namespace Jzpeepz\Dynamo;

use Illuminate\Support\Str;

class DynamoField
{
    private $attributes = [];

    /**
     * Closure that overrides the form field
     * @var Closure
     */
    private $viewHandler = null;

    public function __construct($attributes = [])
    {
        $this->attributes = $attributes;
    }

    public function __get($name)
    {
        return isset($this->attributes[$name]) ? $this->attributes[$name] : null;
    }

    public function __set($name, $value)
    {
        $this->attributes[$name] = $value;
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
        $display = true;

        $displayClosure = $this->getOption('display');

        if (!empty($displayClosure)) {
            $display = call_user_func($displayClosure, $item);
        }

        if ($this->render) {
            if ($this->hasViewHandler()) {
                return call_user_func($this->getViewHandler(), $item, $this);
            }

            return view('dynamo::' . $this->getThemePrefix() . 'partials.fields.' . $this->type, ['field' => $this, 'item' => $item, 'display' => $display])->render();
        }
    }

    public function renderStub()
    {
        if ($this->render) {
            return view('dynamo::' . $this->getThemePrefix() . 'stubs.partials.fields.' . $this->type, ['field' => $this])->render();
        }
    }

    public function getOption($key)
    {
        if ($key == 'maxlength'
            && $this->type == 'text'
            && (!isset($this->options['maxlength'])
                || (isset($this->options['maxlength'])
                && empty($this->options['maxlength'])))
        ) {
            $this->attributes['options']['maxlength'] = 255;
        }

        return isset($this->options[$key]) ? $this->options[$key] : '';
    }

    public function setOption($key, $value)
    {
        $this->attributes['options'][$key] = $value;
    }

    public function hasOption()
    {
        return !$this->options[$key];
    }

    public function getValue($item)
    {
        $key = $this->key;

        // check to see if the field has a callable for formatting
        if (is_callable($this->formatCallable)) {
            return call_user_func($this->formatCallable, $item);
        }

        // check to see if the key ends with '_id' meaning a refence to another model
        $lastThree = substr($key, strlen($key) - 3);
        if ($lastThree == '_id') {
            $class = '\\App\\' . Str::studly(str_replace($lastThree, '', $key));

            if (class_exists($class)) {
                $model = $class::find($item->$key);
                return $model;
            }
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

    public function getSelectOption($index)
    {
        $options = $this->getSelectOptions();

        return isset($options[$index]) ? $options[$index] : null;
    }

    public function hasViewHandler()
    {
        return !empty($this->viewHandler) && is_callable($this->viewHandler);
    }

    public function setViewHandler($handler)
    {
        $this->viewHandler = $handler;

        return $this;
    }

    public function getViewHandler()
    {
        return $this->viewHandler;
    }

    public function getThemePrefix()
    {
        $theme = config('dynamo.view_theme');

        return !empty($theme) ? $theme . '.' : '';
    }

    public function getHtmlAttributes($defaultAttributes = [])
    {
        $customAttributes = $this->getOption('attributes');

        if (empty($customAttributes)) {
            $customAttributes = [];
        }

        return array_merge($defaultAttributes, $customAttributes);
    }

    public function isRequired()
    {
        $attributes = $this->getOption('attributes');

        return isset($attributes['required']) && $attributes['required'] == true;
    }
}
