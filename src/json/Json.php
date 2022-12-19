<?php

namespace FT\Attributes\Json;

use Attribute;
use BackedEnum;
use FT\Attributes\Json\Conversion\ConversionService;
use FT\Attributes\Json\Exceptions\JsonException;
use FT\Reflection\Attributes\Inheritable;
use FT\Reflection\ReflectionUtils;
use ReflectionEnum;
use stdClass;
use UnitEnum;

#[Inheritable]
#[Attribute(Attribute::TARGET_CLASS)]
final class Json {
    public function __construct(
        public readonly array $ignored_properties = [],
        public readonly JsonInclude $include = JsonInclude::ALL,
        public readonly array | null $property_order = null
    )
    { }

    public function is_ignorable(string $property) {
        return in_array($property, $this->ignored_properties);
    }

    private static function resolve(JsonPropertyDescriptor $pd, $value) {
        $value_class = ReflectionUtils::get_class_name($value);
        $target_class = $pd->get_class_name();

        $final = null;
        if ($target_class === null || $value_class === null && $target_class === null)
            $final = $value;
        else if (ConversionService::can_covert($value_class, $target_class))
            $final = ConversionService::convert($value, $target_class, $pd);
        else if (ConversionService::can_covert($value_class, 'string'))
            $final = ConversionService::convert($value, 'string', $pd);
        else if ($value instanceof BackedEnum) {
            $enum = new class{};
            $enum->name = $value->name;
            $enum->value = $value->value;
            $final = $enum;
        }
        else if ($value instanceof UnitEnum)
            $final = $value->name;
        else if (is_object($value)) {
            $target = JsonCache::get($pd->get_class_name());
            $target_resolved = Json::jsonify($target, $value);

            if ($pd->has_json_unwrapped) {
                $final = [];
                foreach ($target_resolved as $key => $value)
                    $final[$key] = $value;

                return $final;
            } else {
                if ($pd->property->type->has_type('array'))
                    $final = $target_resolved;
                else if (empty($target_resolved)) $final = null;
                else $final = $target_resolved;
            }
        } else $final = $value;

        if (is_string($final))
            $final = utf8_encode($final);
        return [$pd->json_key => $final];
    }

    private static function jsonify(JsonCachedClass $class, object $object) {
        $out = [];
        foreach ($class->resolved_properties as $pd) {
            if (!$pd->property->delegate->isInitialized($object)) {
                $value = $pd->getDefault();

                if ($value === null && $class->config->include !== JsonInclude::NON_NULL)
                    $out[$pd->json_key] = null;
                else $out = array_merge($out, static::resolve($pd, $value));

                continue;
            }

            $value = $pd->property->get_value($object);

            if ($value === null && $pd->has_json_default)
                $value = $pd->getDefault();

            if (empty($value) && $class->config->include === JsonInclude::NON_EMPTY) continue;
            else $out = array_merge($out, static::resolve($pd, $value));
        }

        return $out;
    }

    private static function decodify(JsonCachedClass $class, object $object) {
        $new = $class->newInstance();

        /**
         * @var JsonPropertyDescriptor[]
         */
        $unwrappeds = [];

        foreach ($class->resolved_properties as $pd) {
            if ($pd->has_json_unwrapped) {
                $target = JsonCache::get($pd->get_class_name());
                $unwrappeds[$pd->name] = ['cache' => $target, 'new' => $target->delegate->newInstance()];
            }
        }

        foreach ($object as $key => $value) {
            $target = $new;
            $target_config = $class->config;

            $pd = $class->find_property_by_key($key);
            if ($pd === null) {
                $pd = $class->find_property_in_wrapped_object($key);

                if ($pd === null)
                    throw new JsonException("$key does not exist on " . $class->name);

                $unwrapped = $unwrappeds[$pd->name];
                $target = $unwrapped['new'];
                $target_config = $unwrapped['cache']->config;
                $pd = $unwrapped['cache']->find_property_by_key($key);
            }

            if ($pd->is_ignored) continue;
            if ($value === null && $target_config->include === JsonInclude::NON_NULL)
                continue;

            if ($pd->property->type->is_enum()) {
                $enum = new ReflectionEnum($pd->get_class_name());
                $target_accessor = "name";
                $fvalue = $value;

                if ($value instanceof stdClass) {
                    if (!property_exists($value, 'name'))
                        throw new JsonException("Deserializing enums requires a name property to exist");

                    $fvalue = $value->name;
                }
                else if ($enum->isBacked()) $target_accessor = "value";

                $match = false;
                foreach ($enum->getCases() as $case) {
                    if ($case->{$target_accessor} === $fvalue) {
                        $pd->property->delegate->setValue($target, $case->getValue());
                        $match = true;
                        break;
                    }
                }

                if (!$match)
                    throw new JsonException("Enum case does not exist for " . $pd->get_qualified_name());
            }
            else if ($value instanceof stdClass) {
                $class_name = $pd->get_class_name() ?? "stdClass";
                $pd->property->delegate->setValue($target, static::decodify(JsonCache::get($class_name), $value));
            }
            else if (is_countable($value)) {
                if (count($value) === 0 && $target_config->include === JsonInclude::NON_EMPTY)
                    continue;

                if ($pd->is_json_array) {
                    $pd_array = [];
                    $unmarshall_class = $pd->property->get_attribute(JsonArray::class)->getArgument('class_name');
                    for ($i=0; $i < count($value); $i++) {
                        $element = $value[$i];
                        if (!($element instanceof stdClass))
                            throw new JsonException("Expecting array index [$i] to be of type $unmarshall_class @ " . $pd->get_qualified_name());

                        $pd_array[] = static::decodify(JsonCache::get($unmarshall_class), $element);
                    }

                    $pd->property->delegate->setValue($target, $pd_array);
                } else $pd->property->delegate->setValue($target, $value);
            }
            else {
                $source_class = ReflectionUtils::get_class_name($value);
                if ($source_class === null) $source_class = gettype($value);

                $target_class = $pd->get_class_name();
                if ($target_class === null) {
                        foreach ($pd->property->type->types as $type)
                            if (ConversionService::can_covert($source_class, $type->getName())) {
                                $target_class = $type->getName();
                                break;
                            }
                }

                if (ConversionService::can_covert($source_class, $target_class))
                    $pd->property->delegate->setValue($target, ConversionService::convert($value, $target_class, $pd));
                else
                    $pd->property->delegate->setValue($target, $value);
            }
        }

        foreach ($unwrappeds as $name => $value)
            $new->{$name} = $value['new'];

        return $new;
    }

    public static function decode(string $json, string $class_name) {
        $decoded = json_decode($json);

        if (json_last_error() !== JSON_ERROR_NONE)
            throw new JsonException(json_last_error_msg());

        return static::decodify(JsonCache::get($class_name), $decoded);
    }

    public static function encode(object $object) {
        $result = Json::jsonify(JsonCache::get($object::class), $object);
        $encoded = json_encode($result);

        if (json_last_error() !== JSON_ERROR_NONE)
            throw new JsonException(json_last_error_msg());

        return $encoded;
    }

}
?>