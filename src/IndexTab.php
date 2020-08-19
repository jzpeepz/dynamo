<?php

namespace Jzpeepz\Dynamo;

class IndexTab extends ModuleTab
{
    public $queryFilter;
    private $showCount = false;

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

    public function getViewName()
    {
        return str_slug($this->getName());
    }

    public function showCount()
    {
        $this->showCount = true;

        return $this;
    }

    public function shouldShowCount()
    {
        return $this->showCount;
    }

}