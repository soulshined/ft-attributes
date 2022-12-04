<?php
namespace Tests\Model;

use FT\Attributes\Lombok\LombokProperties;

class FooWithEnums {
    use LombokProperties;

    private BasicEnum $basic_null;
    private IntEnum $int_null;
    private StringEnum $string_null;

    private BasicEnum $basic;
    private IntEnum $int;
    private StringEnum $string;

    public function __construct()
    {
        $this->basic = BasicEnum::A;
        $this->int = IntEnum::B;
        $this->string = StringEnum::C;
    }

}
?>