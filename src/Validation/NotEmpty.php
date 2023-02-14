<?php

namespace FT\Attributes\Validation;

use Attribute;
use FT\Reflection\Property;

#[Attribute(Attribute::TARGET_PROPERTY)]
final class NotEmpty implements ValidationAware
{

    public function __construct(public readonly ?string $message = null) { }

    public function validate(Property $property, mixed $value): ?IllegalArgumentException
    {
        $error = new IllegalArgumentException($this->message ?? $property->get_qualified_name() . " is not expected to be empty");

        if (!isset($value)) return null;
        if ($value === null) return $error;

        if (is_string($value) && strlen(trim($value)) === 0 ||
            is_countable($value) && count($value) === 0)
                return $error;

        return null;
    }

}
