<?php

namespace FT\Attributes\Json;

use Exception;
use FT\Attributes\ClassCache;
use FT\Attributes\Reflection\ManagedType;

abstract class JsonCache {
    /**
     * @var JsonCachedClass[]
     */
    private static array $cache;

    private static function init() {
        static::$cache = [];
    }

    public static function get(string $class) : JsonCachedClass {
        $target = ClassCache::get($class);

        if (key_exists($target->name, static::$cache))
            return static::$cache[$target->name];

        $resolved = new JsonCachedClass($target);
        if (!$target->delegate->isAnonymous())
            static::$cache[$target->name] = $resolved;
        return $resolved;
    }

}

final class JsonCachedClass {
    public readonly Json $config;

    /**
     * @var JsonPropertyDescriptor[]
     */
    public readonly array $resolved_properties;

    public function __construct(public readonly ManagedType $type)
    {
        $new = new Json();
        $config = $type->get_attribute(Json::class);
        if ($config !== null)
            $new = $config->newInstance();

        $this->config = $new;

        $prop_names = array_map(fn ($i) => $i->name, $type->properties);
        $order = isset($this->config->property_order) ? $this->config->property_order : $prop_names;
        $fprops = [];
        foreach ($order as $name) {
            $index = array_search($name, $prop_names);
            if ($index === false)
                throw new Exception("$name does not exist on " . $type->name);

            $pd = new JsonPropertyDescriptor($type->properties[$index]);
            $fprops[$pd->json_key] = $pd;
        }

        foreach (array_diff($prop_names, array_keys($fprops)) as $name) {
            $index = array_search($name, $prop_names);
            $pd = new JsonPropertyDescriptor($type->properties[$index]);
            $fprops[$pd->json_key] = $pd;
        }

        $this->resolved_properties = array_values(array_filter(
            $fprops,
            fn ($i) => !$this->config->is_ignorable($i->name) && !$i->is_ignored
        ));
    }

    public function find_property_by_key(string $key) : ?JsonPropertyDescriptor {
        return array_first(fn ($i) => $i->json_key === $key, $this->resolved_properties);
    }

    public function find_property_in_wrapped_object(string $key) : ?JsonPropertyDescriptor {
        foreach ($this->resolved_properties as $pd) {
            if (!$pd->has_json_unwrapped) continue;

            $candidate = JsonCache::get($pd->get_class_name());
            $prop = $candidate->find_property_by_key($key);
            if ($prop !== null) return $pd;
        }
        return null;
    }
}

(static function () {
    static::init();
})->bindTo(null, JsonCache::class)();

?>