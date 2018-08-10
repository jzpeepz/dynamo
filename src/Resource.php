<?php

namespace Jzpeepz\Dynamo;

class Resource {

    public function getModelClass()
    {
        $class = get_class($this);

        return str_replace('App\\Dynamo', 'App', $class);
    }

    public function getModelInstance($primaryKey = null)
    {
        $class = '\\' . $this->getModelClass();

        if (empty($primaryKey)) {
            return new $class;
        } else {
            return $class::find($primaryKey);
        }
    }

}
