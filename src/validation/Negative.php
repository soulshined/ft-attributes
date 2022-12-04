<?php

namespace FT\Attributes\Validation;

use Attribute;
use FT\Attributes\Reflection\PropertyDescriptor;

#[Attribute(Attribute::TARGET_PROPERTY)]
final class Negative implements ValidationAware
{

    public function __construct(public readonly ?string $message = null) { }

    public function validate(PropertyDescriptor $pd, mixed $value): ?IllegalArgumentException
    {
        if (!is_numeric($value)) return null;

        return $value + 0 < 0
            ? null
            : new IllegalArgumentException($this->message ?? $pd->get_qualified_name() . " is not a negative number");
    }

}
