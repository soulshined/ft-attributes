<?php

namespace Tests\Model;

use Attribute;
use FT\Attributes\Reflection\PropertyDescriptor;
use FT\Attributes\Validation\IllegalArgumentException;
use FT\Attributes\Validation\ValidationAware;

#[Attribute(Attribute::TARGET_PROPERTY)]
final class NotNoAttribute implements ValidationAware {

    public function validate(PropertyDescriptor $pd, mixed $value): ?IllegalArgumentException
    {
        if ($value === 'no')
            return new IllegalArgumentException("Not allowed to be no");

        return null;
    }

}

?>