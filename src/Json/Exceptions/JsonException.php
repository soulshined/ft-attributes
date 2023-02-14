<?php

namespace FT\Attributes\Json\Exceptions;

use RuntimeException;

class JsonException extends RuntimeException {

    public function __construct(string $message)
    {
        parent::__construct($message);
    }

}