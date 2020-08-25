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
    private $searchOptions = null;
    private $deleteHidden = true;
    private $addHidden = true;
    private $currentGroup = null;
    private $groupLabels = [];
    private $position = 10;
    private $render = true;
    private $indexTabs = null;
    private $indexButtons = null;
    private $indexPanelTitleText = null;
    private $formPanelTitleText = null;
    private $saveItemText = null;
    private $addItemText = null;
    private $routeParameters = [];
    private $ignoredScopes = null;
    private $indexFilters = null;
    private $handlers = null;
    private $actionButtons = null;

    public function __construct($class)
    {
        $this->class = $class;

        $this->indexes = collect();
        $this->fields = collect();
        $this->indexOrderBy = collect();
        $this->searchable = collect();
        $this->searchOptions = collect();
        $this->indexTabs = collect();
        $this->indexButtons = collect();
        $this->indexFilters = collect();
        $this->handlers = collect();
        $this->actionButtons = collect();
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
        if (!$item->exists) {
            if (property_exists($item, 'keyFields')) {
                foreach ($item->keyFields as $field) {
                    $item->$field = $data[$field];
                }
            }
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

    public function getRouteUrl($action)
    {
        return redirect()->route($this->getRoute($action), $this->getRouteParameters($action));
    }

    public function getRouteParameters($action)
    {
        return isset($this->routeParameters[$action]) ? $this->routeParameters[$action] : null;
    }

    public function setRouteParameters($action, $parameters = [])
    {
        $this->routeParameters[$action] = $parameters;
        return $this;
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

    public function getIndexItemsQueryBuilder($view = null)
    {
        
        $className = $this->class;

        // $query = $className::withoutGlobalScopes($this->getIgnoredScopes());
        $query = $className::query();

        $query = $this->executeViewFilter($query, $view);

        // do any searching
        if (!$this->searchable->isEmpty() && request()->has('q')) {
            $query = $query->where(
                DB::raw('CONCAT(' . $this->getSearchableKeys()->implode(", ' ', ") . ')'),
                'like',
                '%' . request()->input('q') . '%'
            );
        }

        // foreach ($this->filters as $filter) {
        //     // apply filters
        //     $query = $filter->modifyQuery($query);
        // }

        foreach ($this->getIndexOrderBy() as $orderBy) {
            $query = $query->orderBy($orderBy['column'], $orderBy['sort']);
        }

        return $query;
    }

    public function getIndexItems()
    {
        $query = $this->getIndexItemsQueryBuilder();

        if (!empty($this->paginate)) {
            $items = $query->paginate($this->paginate);
        } else {
            $items = $query->get();
        }

        return $items;
    }

    public function executeViewFilter($query, $view = null)
    {
        if (empty($view)) {
            $view = request()->input('view');
        }
       
        $tab = $this->getIndexTab($view);

        if (!empty($tab)) {
            $query = call_user_func($tab->queryFilter, $query);
        }

        // apply index filters
        foreach ($this->indexFilters as $filter) {
            $query = call_user_func($filter, $query);
        }

        return $query;
    }

    public function addIndexFilter(callable $filter)
    {
        $this->indexFilters->push($filter);
        return $this;
    }

    public function ignoredScopes(array $scopes = [])
    {
        if (!is_array($scopes)) {
            $scopes = [$scopes];
        }

        $this->ignoredScopes = $scopes;

        return $this;
    }

    public function applyScopes()
    {
        return $this->ignoredScopes();
    }

    public function getIgnoredScopes()
    {
        return $this->ignoredScopes;
    }

    public function handleSpecialFields($item, $data = [])
    {
        $processedHasMany = [];

        foreach ($data as $key => $value) {

            if (is_object($value) && (get_class($value) == "Illuminate\Http\UploadedFile" || get_class($value) == "Symfony\Component\HttpFoundation\File\UploadedFile")) {
                // handle uploaded files
                $fileName = str_replace('.'.$value->getClientOriginalExtension(), '', $value->getClientOriginalName());
                $destinationFileName = str_slug($fileName).'-'.rand(10000, 99999).'.'.strtolower($value->getClientOriginalExtension());

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

            // handle has many relationships
            if (is_array($value) && method_exists($item, $key)) {
                $syncables = $data[$key];
                $item->{$key}()->sync($syncables);
                unset($data[$key]);
                $processedHasMany[] = $key;
            }

        }

        // handle empty has many fields
        foreach ($this->getFieldsByType('hasMany') as $field) {
            if (!isset($data[$field->key]) && !in_array($field->key, $processedHasMany)) {
                $item->{$field->key}()->detach();
            }
        };

        return $data;
    }

    public function getFieldsByType($type)
    {
        $fields = $this->getAllFields();

        // dd($fields);

        return $fields->filter(function ($field) use ($type) {
            return $field->type == $type;
        });
    }

    public function getAllFields()
    {
        // base object fields
        return $this->fields;

        // group fields
        // $groupFields = $this->groups->reduce(function ($allFields = null, $group = null) {
        //     if (is_null($allFields)) {
        //         $allFields = collect();
        //     }

        //     return $allFields->merge($group->fields);
        // });

        // handle null collection
        // if (is_null($groupFields)) {
        //     $groupFields = collect();
        // }

        // // tab fields and groups within tabs
        // $tabFields = $this->formTabs->reduce(function ($allFields = null, $tab = null) {
        //     if (is_null($allFields)) {
        //         $allFields = collect();
        //     }

        //     $tabAndGroupFields = collect();

        //     foreach ($tab->fields as $tabField) {
        //         if ($tabField->type == 'group') {
        //             $tabAndGroupFields = $tabAndGroupFields->merge($tabField->getOption('group')->fields);
        //         } else {
        //             $tabAndGroupFields = $tabAndGroupFields->merge([$tabField]);
        //         }
        //     }

        //     return $allFields->merge($tabAndGroupFields);
        // });

        // handle null collection
        if (is_null($tabFields)) {
            $tabFields = collect();
        }

        // merge all field collections and return
        return $dynamoFields->merge($groupFields)->merge($tabFields);
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

    public function searchOptions($options = [])
    {
        $this->searchOptions = collect($options);
        return $this;
    }

    public function getSearchOptions()
    {
        return $this->searchOptions;
    }

    public function getSearchOptionsString()
    {
        //take this dynamo's searchOptions array and convert it into a string with spaces inbetween each options
        $arr = [];
        $options = $this->searchOptions;

        foreach ($options as $key => $value) {
            $arr[] = "$key = \"$value\"";
        }

        return implode(' ', $arr);
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

    public function hasIndexTabs()
    {
        return !$this->indexTabs->isEmpty();
    }

    public function getIndexTabs()
    {
        return $this->indexTabs;
    }

    public function getIndexTab($key)
    {
        return $this->indexTabs->where('key', $key)->first();
    }

    public function indexTab(IndexTab $indexTab)
    {
        $this->indexTabs->push($indexTab);

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

    public function addIndexButton(\Closure $button)
    {
        $this->indexButtons->push($button);

        return $this;
    }

    public function getIndexButtons()
    {
        return $this->indexButtons;
    }

    public function hasIndexPanelTitleOverride()
    {
        return isset($this->indexPanelTitleText);
    }

    public function setIndexPanelTitle($value)
    {
        if (is_callable($value)) {
            $this->indexPanelTitleText = call_user_func($value);
        } else {
            $this->indexPanelTitleText = $value;
        }

        return $this;
    }

    public function getIndexPanelTitleOverride()
    {
        return $this->indexPanelTitleText;
    }

    public function hasFormPanelTitleOverride()
    {
        return isset($this->formPanelTitleText);
    }

    public function setFormPanelTitle($value)
    {
        if (is_callable($value)) {
            $this->formPanelTitleText = call_user_func($value);
        } else {
            $this->formPanelTitleText = $value;
        }

        return $this;
    }

    public function getFormPanelTitleOverride()
    {
        return $this->formPanelTitleText;
    }

    public function hasSaveItemTextChange()
    {
        return isset($this->saveItemText);
    }

    public function setSaveItemText($value)
    {
        $this->saveItemText = $value;

        return $this;
    }

    public function getSaveItemText()
    {
        return $this->saveItemText;
    }

    public function hasAddItemTextChange()
    {
        return isset($this->addItemText);
    }

    public function setAddItemText($value)
    {
        $this->addItemText = $value;

        return $this;
    }

    public function getAddItemText()
    {
        return $this->addItemText;
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

    public function setGroupLabel($label)
    {
        $this->groupLabels[$this->currentGroup] = $label;
    }

    public function hasGroupLabel($group)
    {
        return isset($this->groupLabels[$group]) && ! empty($this->groupLabels[$group]);
    }

    public function getGroupLabel($group)
    {
        return $this->hasGroupLabel($group) ? $this->groupLabels[$group] : null;
    }

    public function hasManySimple($key, $options = [])
    {
        $modelClass = '\\App\\' . str_singular(studly_case($key));

        $options['modelClass'] = isset($options['modelClass']) ? $options['modelClass'] : $modelClass;

        $options['nameField'] = isset($options['nameField']) ? $options['nameField'] : 'name';

        $options['options'] = isset($options['options']) ? $options['options'] : $options['modelClass']::orderBy($options['nameField'])->lists($options['nameField'], 'id');

        $options['class'] = isset($options['class']) ? $options['class'] : config('dynamo.default_has_many_class');

        return $this->addField($key, 'hasMany', $options);
    }

    public function addHandler($field, $closure)
    {
        $this->handlers->put($field, $closure);

        return $this;
    }

    public function getHandlers()
    {
        return $this->handlers;
    }

    public static function getGlobalHandlers()
    {
        if (empty(static::$globalHandlers)) {
            return collect();
        }

        return static::$globalHandlers;
    }

    public function getFieldTypeByKey($key)
    {
        foreach ($this->fields as $field) {
            if ($field->key == $key) {
                return $field->type;
            }
        }

        return null;
    }

    public function addActionButton(\Closure $button)
    {
        $this->actionButtons->push($button);

        return $this;
    }

    public function getActionButtons()
    {
        return $this->actionButtons;
    }
}
