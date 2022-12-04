<?php
namespace Tests\Model;

use FT\Attributes\Json\Json;
use FT\Attributes\Json\JsonIgnore;

#[Json(ignored_properties: ['encrypted_data'])]
class FooWithClassAndLocalIgnored extends Foo {

    private string $encrypted_data;
    #[JsonIgnore]
    private string $ssn;
    private string $phone_number;

    public function __construct()
    {
        $this->encrypted_data = random_bytes(10);
        $this->ssn = "xxx-xxx-xxxx";
        $this->phone_number = "xxx-xxx-xxxx";
    }

}
