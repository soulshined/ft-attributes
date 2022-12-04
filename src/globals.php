<?php

use FT\Attributes\Json\Conversion\Base\DateTimeImmutableToStringConverter;
use FT\Attributes\Json\Conversion\Base\DateTimeToStringConverter;
use FT\Attributes\Json\Conversion\Base\IntToDateTimeConverter;
use FT\Attributes\Json\Conversion\Base\IntToDateTimeImmutableConverter;
use FT\Attributes\Json\Conversion\Base\StringToDateTimeConverter;
use FT\Attributes\Json\Conversion\Base\StringToDateTimeImmutableConverter;
use FT\Attributes\Json\Conversion\ConversionService;

//no time zone/offset
define('DATE_RFC3339_NTZ', 'Y-m-d\TH:i:s');

ConversionService::add_converters(
    new DateTimeToStringConverter,
    new DateTimeImmutableToStringConverter,
    new StringToDateTimeConverter,
    new StringToDateTimeImmutableConverter,
    new IntToDateTimeConverter,
    new IntToDateTimeImmutableConverter
);

/**
 * return the first element that matches predicate or null otherwise
 */
function array_first(callable $callback, array $array) : mixed {
    foreach ($array as $key => $value) {
        if (call_user_func($callback, $value))
            return $value;
    }

    return null;
}
?>