<?php

namespace FT\Attributes\Validation;

use Attribute;
use FT\Reflection\Property;

#[Attribute(Attribute::TARGET_PROPERTY)]
final class Positive implements ValidationAware
{
    public function __construct(public readonly ?string $message = null) { }

    public function validate(Property $property, mixed $value): ?IllegalArgumentException
    {
        if (!is_numeric($value)) return null;

        return $value + 0 >= 0
            ? null
            : new IllegalArgumentException($this->message ?? $property->get_qualified_name() . " is not a positive number");
    }
}
