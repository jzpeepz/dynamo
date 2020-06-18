<?php

namespace Jzpeepz\Dynamo;

use DB;
use Storage;
use Illuminate\Support\Str;

class Dynamo
{
    public $label;
    public $class;
    private $indexes = null;
    private $fields = null;
    private $groups = null;
    private $indexOrderBy = null;
    private $paginate = 200;
    private $searchable = null;
    private $searchOptions = null;
    private $addHidden = true;
    private $editHidden = true;
    private $deleteHidden = true;
    private $currentGroup = null;
    private $groupLabels = [];
    private $position = 10;
    private $render = true;
    private $handlers = null;
    private $filters = null;
    private $indexTabs = null;
    private $formTabs = null;
    private $indexButtons = null;
    private $actionButtons = null;
    private $formHeaderButtons = null;
    private $formFooterButtons = null;
    private $ignoredScopes = null;
    private $indexPanelTitleText = null;
    private $formPanelTitleText = null;
    private $saveItemText = null;
    private $addItemText = null;
    public static $globalHandlers = null;
    private $modifier = null;
    private $allInputsDisabled = false;
    private $routeParameters = [];

    public function __construct($class)
    {
        $this->class = $class;

        $this->indexes = collect();
        $this->fields = collect();
        $this->groups = collect();
        $this->indexOrderBy = collect();
        $this->searchable = collect();
        $this->handlers = collect();
        $this->filters = collect();
        $this->searchOptions = collect();
        $this->indexTabs = collect();
        $this->formTabs = collect();
        $this->indexButtons = collect();
        $this->actionButtons = collect();
        $this->formHeaderButtons = collect();
        $this->formFooterButtons = collect();
    }

    public function __call($name, $arguments)
    {
        $type = $name;
        $key = isset($arguments[0]) ? $arguments[0] : null;
        $options = isset($arguments[1]) ? $arguments[1] : [];
        $viewHandler = isset($arguments[2]) ? $arguments[2] : null;
        // $index = isset($arguments[1]) ? $arguments[1] : true;
        // $label = isset($arguments[2]) ? $arguments[2] : null;

        return $this->addField($key, $type, $options, $viewHandler);
    }

    public static function make($class)
    {
        return new static($class);
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

    public function alias($name)
    {
        $this->alias = $name;

        return $this;
    }

    public function getName()
    {
        $baseClassName = $this->getBaseClass();

        if (!empty($this->alias)) {
            return $this->alias;
        }

        return $this->makeLabel(Str::snake($baseClassName));
    }

    public function getBaseClass()
    {
        return class_basename($this->class);
    }

    public function getRoute($action)
    {
        return config('dynamo.route_prefix') . strtolower($this->getBaseClass()) . '.' . $action;
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
        return ucwords(str_replace(['_', '-'], ' ', $key));
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

    public function addIndexes()
    {
        $args = func_get_args();

        foreach ($args as $arg) {
            $this->addIndex($arg);
        }

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

    public function addField($key, $type, $options = [], $customViewHandler = null)
    {
        // $this->removeField($key);
        $existingField = $this->getField($key);

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

        // set disabled attribute if all inputs are disabled
        if ($this->allInputsDisabled) {
            if (!isset($options['attributes'])) {
                $options['attributes'] = [];
            }

            $options['attributes']['disabled'] = true;
        }

        $field = DynamoField::make([
            'key' => $key,
            'type' => $type,
            'onIndex' => $onIndex,
            'label' => empty($label) ? $this->makeLabel($key) : $label,
            'group' => $this->currentGroup,
            'position' => $position,
            'render' => $render,
            'options' => $options,
            'formatCallable' => $formatCallable,
        ]);

        $field->setViewHandler($customViewHandler);

        $fieldKey = $this->fields->search(function ($item, $index) use ($key) {
            return $item->key == $key;
        });

        if ($fieldKey === false) {
            $this->fields->push($field);
        } else {
            $field->position = isset($options['position']) ? $options['position'] : $existingField->position;
            $this->fields[$fieldKey] = $field;
        }

        return $this;
    }

    public function removeField($fieldKey)
    {
        $this->fields = $this->fields->filter(function ($field, $key = null) use ($fieldKey) {
            return $field->key != $fieldKey;
        });

        // remove field from groups
        $this->fields->transform(function ($field, $key) use ($fieldKey) {
            if ($field->type == 'group') {
                $group = $field->getOption('group');
                $group->removeField($fieldKey);
                $field->setOption('group', $group);
            }

            return $field;
        });

        return $this;
    }

    public function removeFields($fieldKeys = [])
    {
        foreach ($fieldKeys as $key) {
            $this->removeField($key);
        }

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

        $types = collect(array_fill_keys($columns, null));

        $types->transform(function ($value, $column) use ($table) {
            return \Schema::getColumnType($table, $column);
        });

        // dd($types);

        foreach ($columns as $column) {
            if (!$item->isGuarded($column) || $item->isFillable($column)) {
                $this->addField($column, $this->getFieldFromType($types[$column]), ['onIndex' => true]);
            }
        }

        // apply all scopes by default
        $this->applyScopes();

        return $this;
    }

    public function getFieldFromType($type)
    {
        if ($type == 'text') {
            return 'textarea';
        }

        return 'text';
    }

    public function paginate($limit)
    {
        $this->paginate = $limit;

        return $this;
    }

    public function getIndexItemsQueryBuilder($view = null)
    {
        $className = $this->class;

        $query = $className::withoutGlobalScopes($this->getIgnoredScopes());

        $query = $this->executeViewFilter($query, $view);

        // do any searching
        if (!$this->searchable->isEmpty() && request()->has('q')) {
            $query = $query->where(
                DB::raw('CONCAT(' . $this->getSearchableKeys()->implode(", ' ', ") . ')'),
                'like',
                '%' . request()->input('q') . '%'
            );
        }

        foreach ($this->filters as $filter) {
            // apply filters
            $query = $filter->modifyQuery($query);
        }

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

        return $query;
    }

    public function handleSpecialFields($item, $data = [])
    {
        $processedHasMany = [];

        foreach ($this->getHandlers() as $key => $handler) {
            call_user_func_array($handler, [&$item, &$data, &$processedHasMany, &$this]);
        }

        $globalHandlers = static::getGlobalHandlers();

        foreach ($data as $key => $value) {
            $fieldType = $this->getFieldTypeByKey($key);

            // ignore any keys that already have handlers assigned
            if (!$this->getHandlers()->has($key) && !$globalHandlers->has($fieldType)) {
                // handle uploaded files
                if (is_object($value) &&
                    (
                        get_class($value) == "Illuminate\Http\UploadedFile" ||
                        get_class($value) == "Symfony\Component\HttpFoundation\File\UploadedFile"
                    )
                ) {
                    $fileName = str_replace(
                        '.' . $value->getClientOriginalExtension(),
                        '',
                        $value->getClientOriginalName()
                    );
                    $destinationFileName = Str::slug($fileName) . '-' . rand(10000, 99999) .
                                        '.' . strtolower($value->getClientOriginalExtension());

                    $disk = Storage::disk(config('dynamo.storage_disk'));
                    $disk->put(
                        config('dynamo.upload_path') . $destinationFileName,
                        file_get_contents($value->getRealPath())
                    );

                    $data[$key] = config('dynamo.upload_path') . $destinationFileName;
                }

                // handle password fields
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

            // Check to see if there is a global handler
            // and they are defined within the users application.
            // These handle all fields of a certain type.
            if ($globalHandlers->has($fieldType)) {
                call_user_func_array($globalHandlers->get($fieldType), [&$item, &$data, $key]);
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

    public function hasSearchable()
    {
        return !$this->searchable->isEmpty();
    }

    public function getSearchableKeys()
    {
        return $this->searchable->map(function ($field, $key) {
            return $field->key;
        });
    }

    public function searchOptions($options = [])
    {
        $this->searchOptions = collect($options);
        return $this;
    }

    public function getPlaceholder()
    {
        return $this->searchPlaceholder;
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
        $type = !empty($type) ? $type : 'text';
        // $searchPlaceholder = !empty($placeHolder) ? $placeHolder : '';

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

    public function hasFormTabs()
    {
        return !$this->formTabs->isEmpty();
    }

    public function getFormTabs()
    {
        return $this->formTabs;
    }

    public function indexTab(IndexTab $indexTab)
    {
        $this->indexTabs->push($indexTab);

        return $this;
    }

    public function formTab(FormTab $formTab)
    {
        $this->formTabs->push($formTab);

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

    public function hideAdd()
    {
        $this->addHidden = false;

        return $this;
    }

    public function hideEdit()
    {
        $this->editHidden = false;

        return $this;
    }

    public function hideDelete()
    {
        $this->deleteHidden = false;

        return $this;
    }

    public function disableAllInputs()
    {
        $this->allInputsDisabled = true;

        return $this;
    }

    public function allInputsDisabled()
    {
        return $this->allInputsDisabled;
    }

    public function addVisible()
    {
        return $this->addHidden;
    }

    public function editVisible()
    {
        return $this->editHidden;
    }

    public function deleteVisible()
    {
        return $this->deleteHidden;
    }

    public function setGroup($name)
    {
        $this->currentGroup = $name;
    }

    public function unsetGroup()
    {
        $this->currentGroup = null;
    }

    // public function group($name, $callable)
    // {
    //     $this->setGroup($name);
    //
    //     call_user_func($callable, $this);
    //
    //     $this->unsetGroup();
    //
    //     return $this;
    // }

    public function getField($key)
    {
        foreach ($this->fields as $field) {
            if ($field->key == $key) {
                return $field;
            }
        }

        return null;
    }

    public function getIndex($key)
    {
        foreach ($this->indexes as $index) {
            if ($index->key == $key) {
                return $index;
            }
        }

        return null;
    }

    public function setGroupLabel($label)
    {
        $this->groupLabels[$this->currentGroup] = $label;

        return $this;
    }

    public function hasGroupLabel($group)
    {
        return isset($this->groupLabels[$group]) && !empty($this->groupLabels[$group]);
    }

    public function getGroupLabel($group)
    {
        return $this->hasGroupLabel($group) ? $this->groupLabels[$group] : null;
    }

    public function hasManySimple($key, $options = [])
    {
        $modelClass = '\\App\\' . Str::singular(Str::studly($key));

        $options['modelClass'] = isset($options['modelClass']) ? $options['modelClass'] : $modelClass;

        $options['nameField'] = isset($options['nameField']) ? $options['nameField'] : 'name';

        $options['options'] = isset($options['options'])
                ? $options['options']
                : $options['modelClass']::orderBy($options['nameField'])->pluck($options['nameField'], 'id');

        $options['class'] = isset($options['class']) ? $options['class'] : config('dynamo.default_has_many_class');

        return $this->addField($key, 'hasMany', $options);
    }

    public function addHandler($field, $closure)
    {
        $this->handlers->put($field, $closure);

        return $this;
    }

    public function removeHandler($field)
    {
        $this->handlers->forget($field);

        return $this;
    }

    public function addFilter($key, $options, $closure, $parameters = [])
    {
        $filter = Filter::make($key, $options, $closure, $parameters);

        $this->filters->put($key, $filter);

        return $this;
    }

    public function getFilters()
    {
        return $this->filters;
    }

    public function getFieldsByType($type)
    {
        $fields = $this->getAllFields();

        return $fields->filter(function ($field, $key) use ($type) {
            return $field->type == $type;
        });
    }

    public function getAllFields()
    {
        // base object fields
        $dynamoFields = $this->fields;

        // group fields
        $groupFields = $this->groups->reduce(function ($allFields = null, $group = null) {
            if (is_null($allFields)) {
                $allFields = collect();
            }

            return $allFields->merge($group->fields);
        });

        // handle null collection
        if (is_null($groupFields)) {
            $groupFields = collect();
        }

        // tab fields and groups within tabs
        $tabFields = $this->formTabs->reduce(function ($allFields = null, $tab = null) {
            if (is_null($allFields)) {
                $allFields = collect();
            }

            $tabAndGroupFields = collect();

            foreach ($tab->fields as $tabField) {
                if ($tabField->type == 'group') {
                    $tabAndGroupFields = $tabAndGroupFields->merge($tabField->getOption('group')->fields);
                } else {
                    $tabAndGroupFields = $tabAndGroupFields->merge([$tabField]);
                }
            }

            return $allFields->merge($tabAndGroupFields);
        });

        // handle null collection
        if (is_null($tabFields)) {
            $tabFields = collect();
        }

        // merge all field collections and return
        return $dynamoFields->merge($groupFields)->merge($tabFields);
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

    public function addActionButton(\Closure $button)
    {
        $this->actionButtons->push($button);

        return $this;
    }

    public function getActionButtons()
    {
        return $this->actionButtons;
    }

    public function addFormHeaderButton(\Closure $button)
    {
        $this->formHeaderButtons->push($button);

        return $this;
    }

    public function getFormHeaderButtons()
    {
        return $this->formHeaderButtons;
    }

    public function hasFormFooterButton()
    {
        return $this->formFooterButtons->isNotEmpty();
    }

    public function addFormFooterButton(\Closure $button)
    {
        $this->formFooterButtons->push($button);

        return $this;
    }

    public function getFormFooterButtons()
    {
        return $this->formFooterButtons;
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

    public static function addGlobalHandler($type, \Closure $handler)
    {
        if (empty(static::$globalHandlers)) {
            $handlers = collect();
            $handlers->put($type, $handler);
            static::$globalHandlers = $handlers;
        } else {
            static::$globalHandlers->put($type, $handler);
        }
    }

    public static function getGlobalHandlers()
    {
        if (empty(static::$globalHandlers)) {
            return collect();
        }

        return static::$globalHandlers;
    }

    public function getHandlers()
    {
        return $this->handlers;
    }

    public function getFieldTypeByKey($key)
    {
        // look the field in the root Dynamo instance
        foreach ($this->fields as $field) {
            if ($field->key == $key) {
                return $field->type;
            }
        }

        // if not found in root Dynamo
        // instance, look in groups and tabs
        $field = $this->findFieldByKey($key);

        if (!empty($field)) {
            return $field->type;
        }

        return null;
    }

    public function findFieldByKey($key)
    {
        // first look in groups on the root Dynamo instance
        foreach ($this->groups as $group) {
            foreach ($group->fields as $field) {
                if ($field->key == $key) {
                    return $field;
                }
            }
        }

        // if not found, look within tabs
        foreach ($this->formTabs as $tab) {
            // within the fields inside of tabs
            foreach ($tab->fields as $field) {
                if ($field->key == $key) {
                    return $field;
                }

                // if the field is a group
                if ($field->type == 'group') {
                    // look at each field within the group
                    foreach ($field->getOption('group')->fields as $groupField) {
                        if ($groupField->key == $key) {
                            return $groupField;
                        }
                    }
                }
            }
        }

        return null;
    }

    public function group($group)
    {
        $this->addField($group->name, 'group', [
            'group' => $group,
        ]);

        $this->addGroup($group);

        return $this;
    }

    public function addGroup($group)
    {
        $this->groups->put($group->name, $group);

        return $this;
    }

    public function getGroup($name)
    {
        $group = $this->groups->get($name);

        if (empty($group)) {
            $group = $this->findGroupInTabs($name);
        }

        return $group;
    }

    public function findGroupInTabs($name)
    {
        foreach ($this->formTabs as $tab) {
            foreach ($tab->fields as $field) {
                if ($field->type == 'group' && $field->key == $name) {
                    return $field->getOption('group');
                }
            }
        }

        return null;
    }

    public function popField()
    {
        return $this->fields->pop();
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

    public function modify($closure)
    {
        $this->modifier = $closure;

        return $this;
    }

    public function getModified($item)
    {
        if (is_callable($this->modifier)) {
            return call_user_func($this->modifier, $this, $item);
        }

        return $this;
    }
}
