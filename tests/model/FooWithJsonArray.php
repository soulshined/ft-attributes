<?php

namespace Tests\Model;

use FT\Attributes\Json\JsonArray;

class FooWithJsonArray {
    #[JsonArray(MyClass::class)]
    public array $myClasses = [];
}
