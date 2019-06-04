<?php

namespace Jzpeepz\Dynamo;

class FieldGroup
{
    public $name;
    public $options;
    public $fields = null;
    private $before;
    private $after;
    private $dynamo;

    public function __construct($name, $options = [])
    {
        $this->name = $name;
        $this->options = collect($options);
        $this->fields = collect();
        $this->before = collect();
        $this->after = collect();
        $this->dynamo = new Dynamo('');
    }

    public function __call($name, $arguments = [])
    {
        $this->dynamo = call_user_func_array([$this->dynamo, $name], $arguments);

        $field = $this->dynamo->popField();

        $this->fields->push($field);

        return $this;
    }

    public static function make($name, $options = [])
    {
        return new static($name, $options);
    }

    public function render($item)
    {
        return $this->fields->reduce(function ($carry, $field) use ($item) {
            return $carry . $field->render($item);
        });
    }

    public function before($html)
    {
        $this->before->push($html);

        return $this;
    }

    public function renderBefore()
    {
        return $this->before->implode('');
    }

    public function after($html)
    {
        $this->after->push($html);

        return $this;
    }

    public function renderAfter()
    {
        return $this->after->implode('');
    }

    public function rowStart()
    {
        return $this->before('<div class="row">');
    }

    public function rowEnd()
    {
        return $this->after('</div>');
    }

    public function row()
    {
        return $this->rowStart()
                    ->rowEnd();
    }

    public function removeField($fieldKey)
    {
        $this->fields = $this->fields->reject(function ($field, $key) use ($fieldKey) {
            return $field->key == $fieldKey;
        });
    }

    public function getOption($key)
    {
        $this->options->get($key);
    }

    public function setOption($key, $value)
    {
        $this->options->set($key, $value);
    }

    public function isEmpty()
    {
        return $this->fields->isEmpty();
    }
}
