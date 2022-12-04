<?php

namespace FT\Attributes\Reflection;

final class ReflectionUtils {

    public static function is_primitive($value) : bool {
        return $value === null || is_scalar($value);
    }

    public static function is_builtin($value) : bool {
        if (in_array(
            gettype($value)
        ,[
            'int',
            'integer',
            'float',
            'double',
            'bool',
            'boolean',
            'string',
            'array',
            'callable',
        ])) return true;

        return false;
    }

    public static function get_class_name($value) : ?string {
        if ($value === null) return null;
        if (is_scalar($value) && !is_string($value)) return null;
        if (is_array($value)) return null;
        if (is_resource($value)) return null;

        $target = is_string($value) ? $value : $value::class;

        if ($target === 'object') return null;

        if (class_exists($target))
            return $target;

        return null;
    }

    public static function is_temporal($value) : bool {
        $class = static::get_class_name($value);
        if ($class === null) return false;

        return in_array($class, [
            'DateTime'
        ]);
    }
}

?>