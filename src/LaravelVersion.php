<?php

namespace Jzpeepz\Dynamo;

class LaravelVersion {

    public static function is($versions = [])
    {
        if (! is_array($versions)) {
            $versions = [$versions];
        }

        $laravel = app();

        foreach ($versions as $version) {
            if ($version == self::getVersion($laravel::VERSION, self::getPrecision($version))) {
                return true;
            }
        }

        return false;
    }

    public static function getVersion($fullVersion, $precision = 2)
    {
        $parts = explode('.', $fullVersion);

        $requested = [];

        for ($i=0; $i < $precision; $i++) {
            $requested[] = $parts[$i];
        }

        return implode('.', $requested);
    }

    public static function getPrecision($version)
    {
        return substr_count($version, '.') + 1;
    }
}
