<?php

namespace FT\Attributes\Json\Conversion;

use FT\Attributes\Json\JsonPropertyDescriptor;
use Monolog\Logger;

abstract class Converter
{
    public readonly Logger $log;

    protected function __construct(public readonly string $source_type, public readonly string $target_type)
    {
        $this->log = new Logger(__CLASS__);
    }

    public abstract function convert(JsonPropertyDescriptor $pd, mixed $value);
}

?>