<?php

namespace Jzpeepz\Dynamo;

use DB;
use Storage;

class Dynamo
{
    public $label;
    public $class;
    private $indexes = null;
    private $fields = null;
    private $indexOrderBy = null;
    private $paginate = 200;
    private $searchable = null;

    public function __construct($class)
    {
        $this->class = $class;

        $this->indexes = collect();
        $this->fields = collect();
        $this->indexOrderBy = collect();
        $this->searchable = collect();
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

    public function store($item)
    {
        $data = request()->all();

        // fill and save so that we have an id for saving uploaded files
        if (! $item->exists) {
            $item->save();
        }

        $data = $this->handleSpecialFields($item, $data);

        // fill and save again
        $item->fill($data);
        $item->save();

        return $item;
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
        return config('dynamo.route_prefix') . strtolower($this->getBaseClass()) . '.' . $action;
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
        $this->indexes = $this->indexes->filter(function ($field, $key = null) use ($indexKey) {
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
        $this->fields = $this->fields->filter(function ($field, $key = null) use ($fieldKey) {
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

        // do any searching
        if (! $this->searchable->isEmpty() && request()->has('q')) {
            $query = $query->where(DB::raw('CONCAT('.$this->getSearchableKeys()->implode(", ' ', ").')'), 'like', '%'.request()->input('q').'%');
        }

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

    public function handleSpecialFields($item, $data = [])
    {
        foreach ($data as $key => $value) {
            if (is_object($value) && get_class($value) == "Illuminate\Http\UploadedFile") {
                // handle uploaded files
                $fileName = str_replace('.'.$value->getClientOriginalExtension(), '', $value->getClientOriginalName());
                $destinationFileName = str_slug($fileName).'.'.strtolower($value->getClientOriginalExtension());

                $disk = Storage::disk(config('dynamo.storage_disk'));
                $disk->put(config('dynamo.upload_path').$destinationFileName, file_get_contents($value->getRealPath()));

                $data[$key] = config('dynamo.upload_path').$destinationFileName;
            }

            if ($key == 'password') {
                // handle password reset
                if (empty($value)) {
                    unset($data['password']);
                } else {
                    $data['password'] = bcrypt($value);
                }
            }
        }

        return $data;
    }

    public function hasSearchable()
    {
        return ! $this->searchable->isEmpty();
    }

    public function getSearchableKeys()
    {
        return $this->searchable->map(function($field, $key){
            return $field->key;
        });
    }

    public function searchable($key, $type = null, $options = [])
    {
        $label = isset($options['label']) ? $options['label'] : null;
        $type = ! empty($type) ? $$type : 'text';

        $this->searchable->push(DynamoField::make([
            'key' => $key,
            'type' => $type,
            'label' => empty($label) ? $this->makeLabel($key) : $label,
            'options' => $options,
        ]));

        return $this;
    }

    public function getValue($key, $item)
    {
        // check to see if the key ends with '_id' meaning a refence to another model
        $lastThree = substr($key, strlen($key)-3);
        if ($lastThree == '_id') {
            $class = '\\App\\'.studly_case(str_replace($lastThree, '', $key));
            $model = $class::find($item->$key);
            return $model;
        }

        return $item->$key;
    }
}
