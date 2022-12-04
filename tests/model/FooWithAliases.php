<?php

namespace Tests\Model;

use FT\Attributes\Json\JsonProperty;

class FooWithAliases extends Foo {

    #[JsonProperty("owner")]
    protected MyClass $myClass;

    #[JsonProperty("amount")]
    protected float $float;

    public function __construct()
    {
        $this->myClass = new MyClass;
    }

}

?>