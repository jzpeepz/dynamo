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
    private $deleteHidden = true;
    private $addHidden = true;
    private $currentGroup = null;
    private $position = 10;
    private $render = true;

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

    public function addIndex($key, $label = null, $formatCallable = null)
    {
        $this->removeIndex($key);

        $this->indexes->push(DynamoField::make([
            'key' => $key,
            'label' => empty($label) ? $this->makeLabel($key) : $label,
            'formatCallable' => $formatCallable,
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

        $onIndex = isset($options['onIndex']) ? $options['onIndex'] : false;
        $label = isset($options['label']) ? $options['label'] : null;
        $position = isset($options['position']) ? $options['position'] : $this->position;
        $render = isset($options['render']) ? $options['render'] : true;
        $formatCallable = isset($options['formatCallable']) ? $options['formatCallable'] : null;

        // increment the global position
        $this->position = $this->position + 10;

        if ($onIndex) {
            $this->addIndex($key, $label, $formatCallable);
        }

        $this->fields->push(DynamoField::make([
            'key' => $key,
            'type' => $type,
            'onIndex' => $onIndex,
            'label' => empty($label) ? $this->makeLabel($key) : $label,
            'group' => $this->currentGroup,
            'position' => $position,
            'render' => $render,
            'options' => $options,
            'formatCallable' => $formatCallable,
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

    public function getFieldGroups()
    {
        $groups = [];

        foreach ($this->fields as $field) {
            $groups[$field->group][] = $field;
        }

        // sort each group
        foreach ($groups as $key => $group) {
            usort($group, function ($a, $b) {
                if ($a->position == $b->position) {
                    return 0;
                }
                return ($a->position < $b->position) ? -1 : 1;
            });

            $groups[$key] = $group;
        }

        return $groups;
    }

    public function auto()
    {
        $item = new $this->class;

        $table = $item->getTable();

        $columns = DB::getSchemaBuilder()->getColumnListing($table);

        foreach ($columns as $column) {
            if (! $item->isGuarded($column) || $item->isFillable($column)) {
                $this->addField($column, 'text', ['onIndex' => true]);
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
        $className = $this->class;

        $query = $className::whereRaw('1=1');

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

            if (is_object($value) && (get_class($value) == "Illuminate\Http\UploadedFile" || get_class($value) == "Symfony\Component\HttpFoundation\File\UploadedFile")) {
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

            if(is_array($value)) {
                $syncables = $data[$key];
                $item->{$key}()->sync($syncables);
                unset($data[$key]);
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
        $field = $this->getField($key);

        return $field->getValue($item);
    }

    public function getIndexValue($key, $item)
    {
        $index = $this->getIndex($key);

        return $index->getValue($item);
    }

    public function hideDelete()
    {
        $this->deleteHidden = false;

        return $this;
    }

    public function hideAdd()
    {
        $this->addHidden = false;

        return $this;
    }

    public function deleteVisible()
    {
        return $this->deleteHidden;
    }

    public function addVisible()
    {
        return $this->addHidden;
    }

    public function setGroup($name)
    {
        $this->currentGroup = $name;
    }

    public function unsetGroup()
    {
        $this->currentGroup = null;
    }

    public function group($name, $callable)
    {
        $this->setGroup($name);

        call_user_func($callable, $this);

        $this->unsetGroup();

        return $this;
    }

    public function getField($key)
    {
        foreach($this->fields as $field) {
            if ($field->key == $key) {
                return $field;
            }
        }

        return null;
    }

    public function getIndex($key)
    {
        foreach($this->indexes as $index) {
            if ($index->key == $key) {
                return $index;
            }
        }

        return null;
    }

}
