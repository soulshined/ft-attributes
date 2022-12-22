<?php


namespace FT\Attributes\Json;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
final class JsonVia
{

    public function __construct(public readonly ?string $getter, public readonly ?string $setter)
    {
    }
}
