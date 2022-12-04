<?php

namespace Tests\Model;

use FT\Attributes\Json\JsonTemporal;
use FT\Attributes\Json\JsonTemporalTypes;

use DateTime;
use DateTimeImmutable;

final class OnlyTemporal {

    #[JsonTemporal(type: JsonTemporalTypes::STRING)]
    private DateTime $dateTimeNoDefault;

    #[JsonTemporal(type: JsonTemporalTypes::NUMBER)]
    private DateTime $dateTimeInteger;

    #[JsonTemporal(type: JsonTemporalTypes::STRING, format: DATE_RFC3339)]
    private DateTime $dateTimeFormat;

    #[JsonTemporal(type: JsonTemporalTypes::STRING, format: 'invalid')]
    private DateTime $dateTimeFormatInvalid;

    #[JsonTemporal(type: JsonTemporalTypes::STRING)]
    private DateTimeImmutable $dateTimeImmutableNoDefault;

    #[JsonTemporal(type: JsonTemporalTypes::NUMBER)]
    private DateTimeImmutable $dateTimeImmutableInteger;

    #[JsonTemporal(type: JsonTemporalTypes::STRING, format: DATE_RFC3339)]
    private DateTimeImmutable $dateTimeImmutableFormat;

    #[JsonTemporal(type: JsonTemporalTypes::STRING, format: 'invalid')]
    private DateTimeImmutable $dateTimeImmutableFormatInvalid;


    public function __construct()
    {
        $this->dateTimeInteger = $GLOBALS['now'];
        $this->dateTimeFormat = $GLOBALS['now'];
        $this->dateTimeFormatInvalid = $GLOBALS['now'];
        $this->dateTimeImmutableInteger = $GLOBALS['inow'];
        $this->dateTimeImmutableFormat = $GLOBALS['inow'];
        $this->dateTimeImmutableFormatInvalid = $GLOBALS['inow'];
    }

}

?>