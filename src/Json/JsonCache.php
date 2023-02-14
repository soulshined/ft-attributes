<?php

namespace FT\Attributes\Json;

use Exception;
use FT\Reflection\ClassCache;
use FT\Reflection\DescriptorMapping;
use FT\Reflection\Type;
use ReflectionClass;

$dms = new DescriptorMapping(type_class: JsonCachedClass::class);

abstract class JsonCache {
    private static DescriptorMapping $mappings;

    private static function init() {
        static::$mappings = new DescriptorMapping(type_class: JsonCachedClass::class);
    }

    public static function get(string $class) : JsonCachedClass {
        return ClassCache::get_with_mappings($class, static::$mappings);
    }

}

final class JsonCachedClass extends Type {
    public readonly Json $config;

    /**
     * @var JsonPropertyDescriptor[]
     */
    public readonly array $resolved_properties;

    public function __construct(ReflectionClass $delegate, DescriptorMapping $mappings)
    {
        parent::__construct($delegate, $mappings);
        $new = new Json();
        $config = $this->get_attribute(Json::class);
        if ($config !== null)
            $new = $config->newInstance();

        $this->config = $new;

        $prop_names = array_map(fn ($i) => $i->name, $this->properties);
        $order = isset($this->config->property_order) ? $this->config->property_order : $prop_names;
        $fprops = [];
        foreach ($order as $name) {
            $index = array_search($name, $prop_names);
            if ($index === false)
                throw new Exception("$name does not exist on " . $this->name);

            $pd = new JsonPropertyDescriptor($this->properties[$index]);
            $fprops[$pd->json_key] = $pd;
        }

        foreach (array_diff($prop_names, array_keys($fprops)) as $name) {
            $index = array_search($name, $prop_names);
            $pd = new JsonPropertyDescriptor($this->properties[$index]);
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

            $class_name = $pd->get_class_name();
            if ($class_name == null) return null;

            $candidate = JsonCache::get($class_name);
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