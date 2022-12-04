<?php

namespace FT\Attributes\Validation;

use Attribute;
use FT\Attributes\Reflection\PropertyDescriptor;

#[Attribute(Attribute::TARGET_PROPERTY)]
final class Size implements ValidationAware {

    public function __construct(public readonly float $min, public readonly float $max = PHP_INT_MAX, public readonly ?string $message = null) { }

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

        if ($transposed_count < $this->min)
            return new IllegalArgumentException($this->message ?? $pd->get_qualified_name() . " size is less than expected ($this->min)");
        if ($transposed_count > $this->max)
            return new IllegalArgumentException($this->message ?? $pd->get_qualified_name() . " size is greater than expected ($this->max)");

        return null;
    }

}

?>