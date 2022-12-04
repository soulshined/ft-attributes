<?php

namespace FT\Attributes\Json;

use FT\Attributes\Reflection\PropertyDescriptor;

final class JsonPropertyDescriptor extends PropertyDescriptor {

    public readonly bool $has_json_default;
    public readonly bool $has_json_temporal;
    public readonly bool $has_json_unwrapped;
    public readonly bool $is_json_array;
    public readonly string $json_key;
    public readonly bool $is_ignored;

    public function __construct(PropertyDescriptor $pd)
    {
        parent::__construct($pd->property);
        $this->has_json_default = $pd->has_attribute(JsonDefault::class);
        $this->has_json_temporal = $pd->has_attribute(JsonTemporal::class);
        $this->is_ignored = $pd->has_attribute(JsonIgnore::class);
        $this->has_json_unwrapped = $pd->has_attribute(JsonUnwrapped::class);
        $this->is_json_array = $pd->has_attribute(JsonArray::class);

        $this->json_key = $pd->has_attribute(JsonProperty::class)
            ? $pd->get_attribute(JsonProperty::class)->getArgument('value')
            : $pd->name;
    }

    public function getDefault() {
        $attr = $this->get_attribute(JsonDefault::class);

        return $attr === null ? null : $attr->getArgument('value');
    }
}

?>