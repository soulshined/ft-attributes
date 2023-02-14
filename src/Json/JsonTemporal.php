<?php


namespace FT\Attributes\Json;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
final class JsonTemporal {

    public function __construct(public readonly JsonTemporalTypes $type, public readonly string | null $format) { }

}
?>