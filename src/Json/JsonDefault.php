<?php
namespace FT\Attributes\Json;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
final class JsonDefault {
    public function __construct(public readonly mixed $value) { }
}

?>