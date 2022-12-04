<?php

namespace Tests\Model;

use FT\Attributes\Json\JsonProperty;
use FT\Attributes\Json\JsonUnwrapped;
use Tests\Model\Foo;

class FooWithEmbeddables extends Foo
{

    #[JsonUnwrapped]
    protected MyClass $myClass;

    #[JsonProperty("amount")]
    protected float $float;

    public function __construct()
    {
        $this->myClass = new MyClass;
    }
}

?>