<?php

namespace FT\Attributes\Log;

use Monolog\Level;
use Attribute;
use DateTimeZone;
use FT\Attributes\PEL\PEL;

#[Attribute(Attribute::TARGET_CLASS)]
final class LogConfig {

    public readonly DateTimeZone $timezone;
    public readonly Level $level;

    public function __construct(
        Level | string $level = Level::Info,
        \DateTimeZone | null $timezone = null,
        public readonly string $format = "%datetime% %channel% [%level_name%] > %message% | %context% %extra%\n"
    ) {
        if ($timezone === null)
            $this->timezone = new DateTimeZone(date_default_timezone_get());
        else $this->timezone = $timezone;

        if (is_string($level)) {
            $str_level = PEL::resolve_placeholders($level);
            $this->level = Level::fromName(empty($str_level) ? 'Info' : $str_level);
        }
        else $this->level = $level;
    }

}
?>