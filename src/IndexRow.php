<?php

namespace Jzpeepz\Dynamo;

class IndexRow
{
    protected $html;
    protected $type = null;

    public function __construct(callable $html)
    {
        $this->html = $html;
    }

    public static function make(callable $html)
    {
        return new static($html);
    }

    public function type($type)
    {
        $this->type = $type;
        return $this;
    }

    public function render($item)
    {
        return call_user_func($this->html, $item);
    }

    public function isType($type)
    {
        return $this->type === $type;
    }
}
