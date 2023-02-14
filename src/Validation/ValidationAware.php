<?php

namespace FT\Attributes\Validation;

use FT\Reflection\Property;

interface ValidationAware {

    public function validate(Property $property, mixed $value) : ?IllegalArgumentException;

}
?>