<?php
namespace FT\Attributes\Validation;

final class CustomPropertyValidators {

    private static array $validators = [];

    public static function add(ValidationAware $validator) {
        static::$validators[] = $validator;
    }

    /**
     * @return ValidationAware[]
     */
    public static function get() {
        return static::$validators;
    }

}


?>