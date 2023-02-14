<?php

namespace FT\Attributes\Lombok;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
final class Getter {
    public function __construct(public readonly AccessLevel $level = AccessLevel::PUBLIC) { }
}

?>