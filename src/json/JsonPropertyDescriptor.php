<?php

namespace FT\Attributes\Json;

use FT\Attributes\Json\Conversion\ConversionService;
use FT\Attributes\Json\Exceptions\JsonException;
use FT\Reflection\Property;
use FT\Reflection\ReflectionUtils;
use ReflectionMethod;
use stdClass;

final class JsonPropertyDescriptor {

    public readonly string $name;
    public readonly bool $has_json_default;
    public readonly bool $has_json_temporal;
    public readonly bool $has_json_unwrapped;
    public readonly bool $is_json_array;
    public readonly string $json_key;
    public readonly bool $is_ignored;
    public readonly bool $is_via;

    public function __construct(public readonly Property $property)
    {
        $this->name = $property->name;
        $this->has_json_default = $property->has_attribute(JsonDefault::class);
        $this->has_json_temporal = $property->has_attribute(JsonTemporal::class);
        $this->is_ignored = $property->has_attribute(JsonIgnore::class);
        $this->has_json_unwrapped = $property->has_attribute(JsonUnwrapped::class);
        $this->is_json_array = $property->has_attribute(JsonArray::class);
        $this->is_via = $property->has_attribute(JsonVia::class);

        $this->json_key = $property->has_attribute(JsonProperty::class)
            ? $property->get_attribute(JsonProperty::class)->getArgument('value')
            : $property->name;
    }

    public function getDefault() {
        $attr = $this->property->get_attribute(JsonDefault::class);

        return $attr === null ? null : $attr->getArgument('value');
    }

    public function get_class_name() : ?string {
        return array_first(fn ($i) => true, $this->property->type->get_class_names());
    }

    private function get_via_method(string $type) : ?ReflectionMethod {
        $target = $this->property->get_attribute(JsonVia::class)->getArgument($type);
        if ($target === null) return null;

        $cls = $this->property->delegate->getDeclaringClass();
        if (!$cls->hasMethod($target))
            throw new JsonException("Method $target does not exist for JsonVia $type");

        return $cls->getMethod($target);
    }

    public function get_value($object) {
        $value = null;
        $class_name = $this->get_class_name();

        if ($this->is_via) {

            $getter = $this->get_via_method('getter');
            if ($getter !== null) {
                $value = $getter->invoke($object);
                $class_name = ReflectionUtils::get_class_name($value);
            }

        } else $value = $this->property->get_value($object);

        return [$value, $class_name];
    }

    public function set_value($instance, $value) {
        $fval = $value;
        if ($this->is_json_array) {
            $unmarshall_class = $this->property->get_attribute(JsonArray::class)->getArgument('class_name');
            if (!is_array($value))
                throw new JsonException($this->property->get_qualified_name() . " is expecting an array of $unmarshall_class got " . gettype($value));

            $fval = [];
            for ($i = 0; $i < count($value); $i++) {
                $element = $value[$i];
                if (!($element instanceof stdClass))
                    throw new JsonException("Expecting array index [$i] to be of type $unmarshall_class @ " . $this->property->get_qualified_name());

                $fval[] = Json::decode(json_encode($element), $unmarshall_class);
            }

        }

        if ($this->is_via) {

            $setter = $this->get_via_method('setter');
            if ($setter !== null) {
                $setter->invoke($instance, $fval);
                return;
            }

        }

        $source_class = ReflectionUtils::get_class_name($value);
        if ($source_class === null) $source_class = gettype($value);

        $target_class = $this->get_class_name();
        if ($target_class === null) {
            foreach ($this->property->type->types as $type)
                if (ConversionService::can_covert($source_class, $type->getName())) {
                    $target_class = $type->getName();
                    break;
                }
        }

        if (ConversionService::can_covert($source_class, $target_class))
            $this->property->delegate->setValue($instance, ConversionService::convert($fval, $target_class, $this));
        else
            $this->property->delegate->setValue($instance, $value);
    }

}

?>