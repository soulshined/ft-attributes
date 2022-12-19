<?php
namespace FT\Attributes\Json\Conversion\Base;

use DateTime;
use FT\Attributes\Json\Conversion\Converter;
use FT\Attributes\Json\JsonPropertyDescriptor;
use FT\Attributes\Json\JsonTemporal;
use FT\Attributes\Json\JsonTemporalTypes;

final class DateTimeToStringConverter extends Converter {

    public function __construct()
    {
        parent::__construct(DateTime::class, 'string');
    }

    public function convert(JsonPropertyDescriptor $pd, mixed $value)
    {
        if ($pd->has_json_temporal) {
            $temporal = $pd->property->get_attribute(JsonTemporal::class);
            $type = $temporal->getArgument('type');
            if ($type === JsonTemporalTypes::NUMBER)
                return $value->getTimestamp();

            return $value->format($temporal->getArgument('format') ?? DATE_RFC3339_NTZ);
        }
        else return $value->format(DATE_RFC3339_NTZ);
    }

}
?>