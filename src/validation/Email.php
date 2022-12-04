<?php
namespace FT\Attributes\Validation;

use FT\Attributes\Reflection\PropertyDescriptor;
use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
final class Email implements ValidationAware {

    public function __construct(public readonly ?string $message = null) { }

    public function validate(PropertyDescriptor $pd, mixed $value): ?IllegalArgumentException
    {
        return filter_var($value, FILTER_VALIDATE_EMAIL) !== false
            ? null
            : new IllegalArgumentException($this->message ?? $pd->get_qualified_name() . " is not a valid email");
    }

}

?>