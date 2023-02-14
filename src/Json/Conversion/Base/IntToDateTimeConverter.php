<?php
namespace FT\Attributes\Json\Conversion\Base;

use DateTime;
use FT\Attributes\Json\Conversion\Converter;
use FT\Attributes\Json\Exceptions\JsonException;
use FT\Attributes\Json\JsonPropertyDescriptor;

final class IntToDateTimeConverter extends Converter {

    public function __construct()
    {
        parent::__construct('integer', DateTime::class);
    }

    public function convert(JsonPropertyDescriptor $pd, mixed $value)
    {
        $date_str = date(DATE_RFC3339_NTZ, $value);

        $date = DateTime::createFromFormat(DATE_RFC3339_NTZ, $date_str);
        if ($date === false)
            throw new JsonException("Can not deserialize date '$value' for {$pd->property->get_qualified_name()}");

        return $date;
    }

}

?>