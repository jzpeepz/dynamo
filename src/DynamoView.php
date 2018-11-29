<?php

namespace Jzpeepz\Dynamo;

class DynamoView
{
    public static function make($dynamo, $view, $params = [])
    {
        // check for overridden view
        $viewFolder = strtolower(str_plural($dynamo->getBaseClass()));
        $shortViewName = str_replace('dynamo::', '', $view);
        $viewName = config('dynamo.view_prefix') . '.' . $viewFolder . '.' . $shortViewName;

        if (view()->exists($viewName)) {
            return view($viewName, $params);
        }

        if (! empty(config('dynamo.view_theme'))) {
            $view = str_replace('::', '::' . config('dynamo.view_theme') . '.', $view);
        }

        return view($view, $params);
    }
}
