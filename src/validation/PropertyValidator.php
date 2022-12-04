<?php

namespace FT\Attributes\Validation;

use FT\Attributes\Observable\ObservableAction;
use FT\Attributes\Observable\ObservableProperties;
use FT\Attributes\Reflection\Attribute;
use FT\Attributes\Reflection\PropertyDescriptor;

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

    private function get_all_validators(PropertyDescriptor $pd) : array {
        $validators = [
            $pd->get_attribute(Email::class),
            $pd->get_attribute(Max::class),
            $pd->get_attribute(Min::class),
            $pd->get_attribute(Negative::class),
            $pd->get_attribute(NotEmpty::class),
            $pd->get_attribute(Pattern::class),
            $pd->get_attribute(Positive::class),
            $pd->get_attribute(Size::class),
            $pd->get_attribute(Url::class),
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