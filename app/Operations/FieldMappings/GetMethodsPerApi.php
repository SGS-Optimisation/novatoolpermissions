<?php

namespace App\Operations\FieldMappings;

use HaydenPierce\ClassFinder\ClassFinder;
use Illuminate\Support\Str;

class GetMethodsPerApi
{
    protected static $skip_methods = [
        "buildBaseUrl",
        "buildRequest",
        "get",
        "parseResponse",
    ];

    protected static $skip_classes = [
        'BaseApi'
    ];


    public $classes = [];

    public static function handle()
    {
        $result = [];
        //return static::getApiClasses();
        foreach(static::getApiClasses() as $class) {
            $reflect = new \ReflectionClass($class);
            $result[$reflect->getShortName()] = array_values(static::getClassMethods($class));
        }

        return $result;
    }

    protected static function getClassMethods($class)
    {
        $methods = collect(get_class_methods($class));

        return $methods->filter(function ($value, $key) {
            return !in_array($value, static::$skip_methods)
                && !Str::endsWith($value, 'Route');
        })->all();
    }

    protected static function getApiClasses()
    {
        $classes = collect(ClassFinder::getClassesInNamespace('App\Services\MySgs\Api'));

        return $classes->filter(function ($value, $key) {
            $keep = true;
            foreach (static::$skip_classes as $class) {
                $keep &= !str_contains($value, $class);
            }

            return $keep;
        })->all();
    }
}
