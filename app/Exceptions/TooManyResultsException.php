<?php

namespace App\Exceptions;

use RuntimeException;
use Throwable;

class TooManyResultsException extends RuntimeException {
    public function __construct(int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct(trans('global.error.too_many_results'), $code, $previous);
    }
}