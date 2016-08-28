<?php

namespace Jzpeepz\Dynamo;

use DB;

class Dynamo
{
    public $label;
    public $class;
    private $indexes = null;
    private $fields = null;
    private $indexOrderBy = null;
    private $paginate = 200;

    public function __construct($class)
    {
        $this->class = $class;

        $this->indexes = collect();
        $this->fields = collect();
        $this->indexOrderBy = collect();
    }

    public function __call($name, $arguments)
    {
        $type = $name;
        $key = isset($arguments[0]) ? $arguments[0] : null;
        $options = isset($arguments[1]) ? $arguments[1] : [];
        // $index = isset($arguments[1]) ? $arguments[1] : true;
        // $label = isset($arguments[2]) ? $arguments[2] : null;

        return $this->addField($key, $type, $options);
    }

    public static function make($class)
    {
        return new Dynamo($class);
    }

    public function getName()
    {
        $baseClassName = $this->getBaseClass();

        return $this->makeLabel(snake_case($baseClassName));
    }

    public function getBaseClass()
    {
        return class_basename($this->class);
    }

    public function getRoute($action)
    {
        return 'admin.' . strtolower($this->getBaseClass()) . '.' . $action;
    }

    private function makeLabel($key)
    {
        return ucwords(str_replace(['_','-'], ' ', $key));
    }

    public function addIndex($key, $label = null)
    {
        $this->removeIndex($key);

        $this->indexes->push(DynamoField::make([
            'key' => $key,
            'label' => empty($label) ? $this->makeLabel($key) : $label,
        ]));

        return $this;
    }

    public function removeIndex($indexKey)
    {
        $this->indexes = $this->indexes->filter(function ($field, $key) use ($indexKey) {
            return $field->key != $indexKey;
        });

        return $this;
    }

    public function getIndexes()
    {
        return $this->indexes;
    }

    public function clearIndexes()
    {
        $this->indexes = collect();

        return $this;
    }

    public function indexOrderBy($column, $sort = 'asc')
    {
        $this->indexOrderBy->push(compact('column', 'sort'));

        return $this;
    }

    public function getIndexOrderBy()
    {
        return $this->indexOrderBy;
    }

    public function addField($key, $type, $options = [])
    {
        $this->removeField($key);

        $index = isset($options['index']) ? $options['index'] : false;
        $label = isset($options['label']) ? $options['label'] : null;

        if ($index) {
            $this->addIndex($key, $label);
        }

        $this->fields->push(DynamoField::make([
            'key' => $key,
            'type' => $type,
            'index' => $index,
            'label' => empty($label) ? $this->makeLabel($key) : $label,
            'options' => $options,
        ]));

        return $this;
    }

    public function removeField($fieldKey)
    {
        $this->fields = $this->fields->filter(function ($field, $key) use ($fieldKey) {
            return $field->key != $fieldKey;
        });

        return $this;
    }

    public function removeBoth($key)
    {
        $this->removeIndex($key);

        $this->removeField($key);

        return $this;
    }

    public function getFields()
    {
        return $this->fields;
    }

    public function auto()
    {
        $item = new $this->class;

        $table = $item->getTable();

        $columns = DB::getSchemaBuilder()->getColumnListing($table);

        foreach ($columns as $column) {
            if (! $item->isGuarded($column) || $item->isFillable($column)) {
                $this->addField($column, 'text', ['index' => true]);
            }
        }

        return $this;
    }

    public function paginate($limit)
    {
        $this->paginate = $limit;

        return $this;
    }

    public function getIndexItems()
    {
        $query = $this->class::whereRaw('1=1');

        foreach($this->getIndexOrderBy() as $orderBy) {
            $query = $query->orderBy($orderBy['column'], $orderBy['sort']);
        }

        if (! empty($this->paginate)) {
            $items = $query->paginate($this->paginate);
        } else {
            $items = $query->get();
        }

        return $items;
    }
}
