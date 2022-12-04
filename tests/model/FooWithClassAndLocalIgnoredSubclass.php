<?php
namespace Tests\Model;

use FT\Attributes\Json\Json;

#[Json(ignored_properties: ['ssn'])]
class FooWithClassAndLocalIgnoredSubclass extends FooWithClassAndLocalIgnored {

    public function __construct()
    {
        parent::__construct();
    }

}
