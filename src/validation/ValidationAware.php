<?php

namespace FT\Attributes\Validation;

use FT\Attributes\Reflection\PropertyDescriptor;

interface ValidationAware {

    public function validate(PropertyDescriptor $pd, mixed $value) : ?IllegalArgumentException;

}
?>