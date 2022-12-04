<?php

namespace FT\Attributes\Json\Conversion\Base;

use DateTime;
use Exception;
use FT\Attributes\Json\Conversion\Converter;
use FT\Attributes\Json\JsonPropertyDescriptor;
use FT\Attributes\Json\JsonTemporal;

final class StringToDateTimeConverter extends Converter {

    public function __construct()
    {
        parent::__construct('string', DateTime::class);
    }

    public function convert(JsonPropertyDescriptor $pd, mixed $value)
    {
        $format = DATE_RFC3339_NTZ;
        if ($pd->has_json_temporal) {
            $temporal = $pd->get_attribute(JsonTemporal::class);
            $format = $temporal->getArgument('format') ?? DATE_RFC3339_NTZ;
        }

        $date = DateTime::createFromFormat($format, $value);
        if ($date === false)
            throw new Exception("Can not deserialize date '$value' with format $format for {$pd->get_qualified_name()}");

        return $date;
    }

}
?>