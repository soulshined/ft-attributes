<?php

namespace FT\Attributes\Validation;

use FT\Attributes\Observable\ObservableAction;
use FT\Attributes\Observable\ObservableProperties;
use FT\Reflection\Attribute;
use FT\Reflection\Property;

trait PropertyValidator {
    use ObservableProperties;

    public function __construct() {
        $this->observe('*', ObservableAction::SET, function ($pd, $value) {
            $validators = $this->get_all_validators($pd);

            if (empty($validators)) return;

            foreach ($validators as $validator)
                $result = $validator->validate($pd, $value);
                if ($result !== null)
                    throw $result;
        });
    }

    private function get_all_validators(Property $property) : array {
        $validators = [
            $property->get_attribute(Email::class),
            $property->get_attribute(Max::class),
            $property->get_attribute(Min::class),
            $property->get_attribute(Negative::class),
            $property->get_attribute(NotEmpty::class),
            $property->get_attribute(Pattern::class),
            $property->get_attribute(Positive::class),
            $property->get_attribute(Size::class),
            $property->get_attribute(Url::class),
            ...CustomPropertyValidators::get()
        ];
        return array_map(function ($i) {
            if ($i instanceof Attribute)
                return $i->newInstance();
            return $i;
        }, array_filter($validators, fn ($i) => $i !== NULL));
    }

}

?>