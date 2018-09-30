<?php

namespace App\Src\Providers\Ryanair;

use Carbon\Carbon;
use App\Src\Providers\RequestTranslator;

/**
 * Translator for the flight search api
 */
class SearchRequestTranslator extends RequestTranslator
{
    protected $keys = [
        'dateFrom' => 'DateOut',
        'destination' => 'Destination',
        'origin' => 'Origin',
    ];
}
