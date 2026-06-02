<?php

namespace App\Exceptions;

use RuntimeException;
use Throwable;

class TooManyResultsException extends RuntimeException {
    public function __construct(int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct('Too many results where found while formatting. Please add more limiting parameters.', $code, $previous);
    }
}