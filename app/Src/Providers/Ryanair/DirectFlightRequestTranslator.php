<?php

namespace App\Src\Providers\Ryanair;

use Carbon\Carbon;
use App\Src\Providers\RequestTranslator;

/**
 * Translator for the direct flight api
 */
class DirectFlightRequestTranslator extends RequestTranslator
{
    protected $keys = [
        'dateFrom' => 'outboundDepartureDateFrom',
        'dateTo' => 'outboundDepartureDateTo',
        'iataFrom' => 'departureAirportIataCode',
        'airportCategory' => 'arrivalAirportCategoryCode',
    ];
}
