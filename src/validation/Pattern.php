<?php

namespace FT\Attributes\Validation;

use Attribute;
use FT\Attributes\Reflection\PropertyDescriptor;

#[Attribute(Attribute::TARGET_PROPERTY)]
final class Pattern implements ValidationAware
{

    public function __construct(public readonly string $value, public readonly ?string $message = null) { }

    public function validate(PropertyDescriptor $pd, mixed $value): ?IllegalArgumentException
    {
        if (!is_string($value)) return null;

        if (!preg_match("/$this->value/", $value, $out))
            throw new IllegalArgumentException($this->message ?? $pd->get_qualified_name() . " does not match pattern " . $this->value);

        return null;
    }
}
