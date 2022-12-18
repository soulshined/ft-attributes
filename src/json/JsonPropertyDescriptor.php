<?php

namespace FT\Attributes\Json;

use FT\Reflection\Property;

final class JsonPropertyDescriptor extends Property {

    public readonly bool $has_json_default;
    public readonly bool $has_json_temporal;
    public readonly bool $has_json_unwrapped;
    public readonly bool $is_json_array;
    public readonly string $json_key;
    public readonly bool $is_ignored;

    public function __construct(Property $property)
    {
        parent::__construct($property->delegate);
        $this->has_json_default = $property->has_attribute(JsonDefault::class);
        $this->has_json_temporal = $property->has_attribute(JsonTemporal::class);
        $this->is_ignored = $property->has_attribute(JsonIgnore::class);
        $this->has_json_unwrapped = $property->has_attribute(JsonUnwrapped::class);
        $this->is_json_array = $property->has_attribute(JsonArray::class);

        $this->json_key = $property->has_attribute(JsonProperty::class)
            ? $property->get_attribute(JsonProperty::class)->getArgument('value')
            : $property->name;
    }

    public function getDefault() {
        $attr = $this->get_attribute(JsonDefault::class);

        return $attr === null ? null : $attr->getArgument('value');
    }

    public function get_class_name() : ?string {
        return array_first(fn ($i) => true, $this->type->get_class_names());
    }

}

?>