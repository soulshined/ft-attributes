<?php

namespace FT\Attributes\Json;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
final class JsonProperty {

    public function __construct(public readonly string $value) { }

}

?>