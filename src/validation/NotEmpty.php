<?php

namespace FT\Attributes\Validation;

use Attribute;
use FT\Attributes\Reflection\PropertyDescriptor;

#[Attribute(Attribute::TARGET_PROPERTY)]
final class NotEmpty implements ValidationAware
{

    public function __construct(public readonly ?string $message = null) { }

    public function validate(PropertyDescriptor $pd, mixed $value): ?IllegalArgumentException
    {
        $error = new IllegalArgumentException($this->message ?? $pd->get_qualified_name() . " is not expected to be empty");

        if (!isset($value)) return null;
        if ($value === null) return $error;

        if (is_string($value) && strlen(trim($value)) === 0 ||
            is_countable($value) && count($value) === 0)
                return $error;

        return null;
    }

}
