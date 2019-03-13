<?php

namespace Jzpeepz\Dynamo;

class IndexTab extends ModuleTab
{
    public $queryFilter;

    public function __construct($name, $queryFilter = null)
    {
        $this->name = $name;
        $this->key = str_slug($name);
        $this->options = collect();

        if (is_null($queryFilter)) {
            $this->queryFilter = function ($query) {
                        return $query;
                    };
        } else {
            $this->queryFilter = $queryFilter;
        }
    }

    public static function make($name, $queryFilter = null)
    {
        return new static($name, $queryFilter);
    }

}
