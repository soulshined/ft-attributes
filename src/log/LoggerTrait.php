<?php

namespace FT\Attributes\Log;

use FT\Attributes\ClassCache;
use FT\Attributes\Log\LogConfig;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;
use Monolog\Registry;

trait LoggerTrait
{

    protected static function get_handler(Level $level, string $format) {
        $handler = new StreamHandler("php://stdout", $level);
        $datetime = "Y-m-d H:i:s";
        $handler->setFormatter(new LineFormatter($format, $datetime));
        return $handler;
    }

    protected static function log() : Logger {
        $cls = ClassCache::get(__CLASS__);
        $config = new LogConfig();

        $last_occurence = strrpos(__CLASS__, '\\');
        $shortname = substr(__CLASS__, $last_occurence === false ? 0 : $last_occurence + 1);

        if ($cls->has_attribute(LogConfig::class))
            $config = $cls->get_attribute(LogConfig::class)->newInstance();

        if (!Registry::hasLogger(__CLASS__))
            Registry::addLogger(new Logger($shortname, [
                static::get_handler($config->level, $config->format)
            ], [], $config->timezone), __CLASS__);

        $instance = Registry::getInstance(__CLASS__);
        return $instance;
    }

}


?>