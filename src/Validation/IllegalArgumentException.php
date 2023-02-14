<?php

namespace FT\Attributes\Validation;

final class IllegalArgumentException extends \Exception {

    public function __construct(string $message)
    {
        parent::__construct($message, 1);
    }

}

?>