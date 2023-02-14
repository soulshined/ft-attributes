<?php
namespace FT\Attributes\Validation;

use Attribute;
use FT\Reflection\Property;

#[Attribute(Attribute::TARGET_PROPERTY)]
final class Max implements ValidationAware {

    public function __construct(public readonly float $value, public readonly ?string $message = null) { }

    public function validate(Property $property, mixed $value): ?IllegalArgumentException
    {
        $transposed_count = 0;
        if (is_countable($value))
            $transposed_count = count($value);
        else if (is_string($value) && !is_numeric($value))
            $transposed_count = strlen($value);
        else if (is_numeric($value))
            $transposed_count = $value + 0;
        else return null;

        return $transposed_count <= $this->value
            ? null
            : new IllegalArgumentException($this->message ?? $property->get_qualified_name() . " is greater than allowed " . $this->value);
    }

}
