<?php

namespace Tests\Model;

use DateTime;
use FT\Attributes\Json\JsonArray;
use FT\Attributes\Json\JsonDefault;
use FT\Attributes\Json\JsonProperty;
use FT\Attributes\Json\JsonTemporal;
use FT\Attributes\Json\JsonTemporalTypes;
use FT\Attributes\Json\JsonUnwrapped;
use FT\Attributes\Json\JsonVia;
use FT\Attributes\Lombok\LombokProperties;

class FooWithJsonVia {
    use LombokProperties;

    public function __construct()
    {
        $this->intEnumTypeCode = 0x0004;
    }

    #[JsonVia(getter: "getEnumType", setter: "setEnumType")]
    #[JsonProperty("intEnumType")]
    private int $intEnumTypeCode;

    #[JsonVia(getter: "getOwnerName", setter: "setOwnerName")]
    #[JsonUnwrapped]
    private MyClass $owner;

    #[JsonVia(getter: "getCodes", setter: "setCodes")]
    private array $codes;

    #[JsonVia(getter: "getOwnerAssociations", setter: "setOwnerAssociations")]
    #[JsonArray(MyClass::class)]
    private array $ownerAssociations;

    #[JsonVia(getter: "getFoos", setter: "setFoos")]
    private array $foos;

    #[JsonDefault([1,2,3,4])]
    #[JsonVia(getter: "withDefault", setter: "setDefault")]
    private mixed $default;

    #[JsonVia(getter: "getTemporal", setter: "setTemporalNum")]
    #[JsonTemporal(type: JsonTemporalTypes::NUMBER)]
    private mixed $temporal_num;

    #[JsonVia(getter: "getTemporal", setter: "setTemporalString")]
    #[JsonTemporal(type: JsonTemporalTypes::STRING, format: DATE_RFC3339_NTZ)]
    private mixed $temporal_str;

    #[JsonVia(getter: "getAliasValue", setter: "setAliasValue")]
    #[JsonProperty("misc")]
    private string $alias;

    public function getAliasValue() {
        return "misc";
    }

    public function setAliasValue($value) {
        $this->alias = $value;
    }

    public function getEnumType() : IntEnum {
        return IntEnum::tryFrom($this->intEnumTypeCode);
    }

    public function setEnumType($code) {
        $this->intEnumTypeCode = $code->value;
    }

    public function getOwnerName() : MyClass {
        return new MyClass("fnamez", "lnamez");
    }

    public function setOwnerName($value) {
        $this->owner = $value;
    }

    public function getOwnerAssociations() : array {
        return [
            new MyClass("assc1", "doe"),
            new MyClass("assc2", "doe"),
            new MyClass("assc3", "doe")
        ];
    }

    public function setOwnerAssociations($value) {
        $this->ownerAssociations = $value;
    }

    public function getFoos() : array {
        return ['bar', 'bazz', 'buzz', 'bizz', 'bozz'];
    }

    public function setFoos($value)  {
        $this->foos = $value;
    }

    public function withDefault() {
        return null;
    }

    public function setDefault($value) {
        $this->default = $value;
    }

    public function getTemporal() {
        global $now;
        return $now;
    }

    public function setTemporalNum($value) {
        $this->temporal_num = $value;
    }

    public function setTemporalString($value) {
        $this->temporal_str = $value;
    }

    public function getCodes() {
        return [99,98,97,96];
    }

    public function setCodes($value) {
        $this->codes = $value;
    }

    public static function new() {
        $f = new FooWithJsonVia;
        $f->alias = "alias";
        $f->codes = ['c','o','d','e','s'];
        $f->temporal_num = new DateTime();
        $f->temporal_str = new DateTime();
        $f->default = "initial";
        $f->foos = ['f','o','o','s'];
        $f->owner = new MyClass('owner', 'ft');
        $f->intEnumTypeCode = IntEnum::F->value;
        $f->ownerAssociations = [];
        return $f;
    }
}

?>