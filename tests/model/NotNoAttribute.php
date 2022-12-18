<?php

namespace Tests\Model;

use Attribute;
use FT\Attributes\Validation\IllegalArgumentException;
use FT\Attributes\Validation\ValidationAware;
use FT\Reflection\Property;

#[Attribute(Attribute::TARGET_PROPERTY)]
final class NotNoAttribute implements ValidationAware {

    public function validate(Property $property, mixed $value): ?IllegalArgumentException
    {
        if ($value === 'no')
            return new IllegalArgumentException("Not allowed to be no");

        return null;
    }

}

?>