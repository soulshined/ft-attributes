<?php

namespace FT\Attributes\Json;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
final class JsonArray {
    public function __construct(public readonly string $class_name) { }
}

?>