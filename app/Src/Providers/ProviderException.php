<?php

namespace App\Src\Providers;

/**
 * Exception wrapper to be used by providers for their irregularities
 */
class ProviderException extends \Exception
{
    const NO_SUCH_ROUTE = 10;
    const SOLD_OUT = 11;
    const NOT_FOUND = 12;
}
