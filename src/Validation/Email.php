<?php
namespace FT\Attributes\Validation;

use Attribute;
use FT\Reflection\Property;

#[Attribute(Attribute::TARGET_PROPERTY)]
final class Email implements ValidationAware {

    public function __construct(public readonly ?string $message = null) { }

    public function validate(Property $property, mixed $value): ?IllegalArgumentException
    {
        return filter_var($value, FILTER_VALIDATE_EMAIL) !== false
            ? null
            : new IllegalArgumentException($this->message ?? $property->get_qualified_name() . " is not a valid email");
    }

}

?>