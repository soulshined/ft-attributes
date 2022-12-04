<?php

namespace FT\Attributes\Validation;

use Attribute;
use FT\Attributes\Reflection\PropertyDescriptor;

#[Attribute(Attribute::TARGET_PROPERTY)]
final class Min implements ValidationAware {

    public function __construct(public readonly float $value, public readonly ?string $message = null) { }

    public function validate(PropertyDescriptor $pd, mixed $value): ?IllegalArgumentException
    {
        $transposed_count = 0;
        if (is_countable($value))
            $transposed_count = count($value);
        else if (is_string($value) && !is_numeric($value))
            $transposed_count = strlen($value);
        else if (is_numeric($value))
            $transposed_count = $value + 0;
        else return null;

        return $transposed_count < $this->value
            ? new IllegalArgumentException($this->message ?? $pd->get_qualified_name() . " is less than allowed minimum " . $this->value)
            : null;
    }

}