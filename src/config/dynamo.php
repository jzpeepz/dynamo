<?php

return [
    'storage_disk' => 'local',

    'upload_path' => '/uploads/',

    'route_prefix' => '',

    'layout' => 'layouts.app',

    'controller_namespace' => 'App\Http\Controllers',

    'controller_path' => app_path('/Http/Controllers'),

    'view_prefix' => 'dynamo',

    'view_theme' => 'bootstrap4',

    'target_blade_section' => 'content',

    'default_has_many_class' => '',

    /*
    |--------------------------------------------------------------------------
    | Model use statements
    |--------------------------------------------------------------------------
    |
    | This value contains an array of the classes that should be imported into
    | the generated model class.
    |
    */
    'model_uses' => [],

    /*
    |--------------------------------------------------------------------------
    | Interfaces that the model implements
    |--------------------------------------------------------------------------
    |
    | This value contains an array of the interfaces that should be
    | implemented by the generated model class.
    |
    */
    'model_implements' => [],

    /*
    |--------------------------------------------------------------------------
    | Model traits
    |--------------------------------------------------------------------------
    |
    | This value contains an array of the traits that should be used by the
    | generated model class.
    |
    */
    'model_traits' => [],
];
