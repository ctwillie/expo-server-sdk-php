<?php

namespace ExpoSDK\Exceptions;

class ExpoException extends \Exception
{
    public $details = null;
    public function __construct(string $message = "", int $code = 0, ?\Throwable $previous = null, $details=null): \Exception {
        $this->details = $details;
        parent::__construct($message, $code, $previous);
    }
}
