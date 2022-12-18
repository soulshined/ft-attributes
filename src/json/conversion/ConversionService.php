<?php

namespace FT\Attributes\Json\Conversion;

use FT\Attributes\Json\Exceptions\JsonException;
use FT\Attributes\Log\LogConfig;
use FT\Attributes\Log\LoggerTrait;

#[LogConfig(level: '{{ getenv("ft.log_level") }}')]
abstract class ConversionService
{
    use LoggerTrait;

    private static array $converters = [];

    public static function can_covert(string $source_class, ?string $target_class): bool
    {
        $target_class = $target_class ?? "null";
        static::log()->debug("Trying to convert $source_class to $target_class");

        $exists = key_exists("$source_class:$target_class", self::$converters);
        if ($exists === true) return true;

        static::log()->debug("$source_class:$target_class doesn't exist");

        foreach (static::$converters as $converter)
            if (is_subclass_of($source_class, $converter->target_type)) return true;

        return false;
    }

    public static function add_converters(Converter ...$converters) {
        foreach ($converters as $c)
            static::add_converter($c);
    }

    public static function add_converter(Converter $converter)
    {
        $key = $converter->source_type . ":" . $converter->target_type;
        if (key_exists($key, self::$converters))
            throw new JsonException("Converter already exists");

        self::$converters[$key] = $converter;
        static::log()->debug("Registered $key converter");
    }

    public static function convert($source, $target_class, $pd)
    {
        $source_class = is_object($source) ? $source::class : gettype($source);
        return self::get_converter($source_class, $target_class)->convert($pd, $source);
    }

    private static function get_converter($source_class, $target_class): Converter
    {
        if (!self::can_covert($source_class, $target_class))
            throw new JsonException("No converter available for $source_class => $target_class");

        return self::$converters["$source_class:$target_class"];
    }
}

?>