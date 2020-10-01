<?php

namespace Jzpeepz\Dynamo;

use Illuminate\Support\Str;

class IndexTab extends ModuleTab
{
    public $queryFilter;
    private $showCount = false;
    private $badgeColor = 'red';

    public function __construct($name, $queryFilter = null)
    {
        $this->name = $name;
        $this->key = Str::slug($name);
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
        return Str::slug($this->getName());
    }

    public function showCount()
    {
        $this->showCount = true;

        return $this;
    }

    public function setBadgeColor($color = 'red')
    {
        $this->badgeColor = $color;

        return $this;
    }

    public function getBadgeColor()
    {
        return $this->badgeColor;
    }

    public function shouldShowCount()
    {
        return $this->showCount;
    }
}
