<?php

namespace FT\Attributes;

use FT\Attributes\Reflection\ManagedType;
use FT\Attributes\Reflection\ReflectionUtils;
use ReflectionClass;
use stdClass;

abstract class ClassCache {
    private static array $cache;

    private static function init() {
        static::$cache = [];
    }

    public static function get(string $class_name) : ManagedType {
        if (key_exists($class_name, static::$cache))
            return static::$cache[$class_name];

        $class_name = ReflectionUtils::get_class_name($class_name);
        $target = class_exists($class_name ?? "") ? $class_name : stdClass::class;

        $rflc = new ReflectionClass($target);
        $mt = new ManagedType($rflc);
        if (!$rflc->isAnonymous())
            static::$cache[$class_name] = $mt;
        return $mt;
    }

}

(static function () {
    static::init();
})->bindTo(null, ClassCache::class)();
?>