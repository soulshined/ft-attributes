<?php

namespace Tests\Model;

class FooWithEmbeddablesSubclass extends FooWithEmbeddables
{
    public function __construct()
    {
        parent::__construct();
    }
}

?>