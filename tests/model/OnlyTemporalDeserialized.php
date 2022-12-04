<?php

namespace Tests\Model;

use FT\Attributes\Json\JsonTemporal;
use FT\Attributes\Json\JsonTemporalTypes;

use DateTime;
use DateTimeImmutable;
use FT\Attributes\Lombok\LombokProperties;

final class OnlyTemporalDeserialized {
    use LombokProperties;

    #[JsonTemporal(type: JsonTemporalTypes::NUMBER)]
    private DateTime $dateTimeInteger;

    #[JsonTemporal(type: JsonTemporalTypes::STRING, format: DATE_RFC3339)]
    private DateTime $dateTimeFormat;

    #[JsonTemporal(type: JsonTemporalTypes::NUMBER)]
    private DateTimeImmutable $dateTimeImmutableInteger;

    #[JsonTemporal(type: JsonTemporalTypes::STRING, format: DATE_RFC3339)]
    private DateTimeImmutable $dateTimeImmutableFormat;

    public function __construct()
    {
        global $now, $inow;

        $this->dateTimeInteger = DateTime::createFromFormat(DATE_RFC3339_NTZ, $now->format(DATE_RFC3339_NTZ));
        $this->dateTimeFormat = DateTime::createFromFormat(DATE_RFC3339_NTZ, $now->format(DATE_RFC3339_NTZ));
        $this->dateTimeImmutableInteger = DateTimeImmutable::createFromFormat(DATE_RFC3339_NTZ, $inow->format(DATE_RFC3339_NTZ));
        $this->dateTimeImmutableFormat = DateTimeImmutable::createFromFormat(DATE_RFC3339_NTZ, $inow->format(DATE_RFC3339_NTZ));
    }

}

?>