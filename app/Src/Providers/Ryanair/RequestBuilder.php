<?php

namespace App\Src\Providers\Ryanair;

use App\Src\Providers\RequestBuilder as BaseBuilder;
use App\Src\Providers\Contracts\DirectFlightBuilder;
use Illuminate\Support\Facades\Config;
use Carbon\Carbon;
use App\Src\Providers\Contracts\FlightSearchBuilder;

/**
 * Ryanair request builder
 */
class RequestBuilder extends BaseBuilder implements
    DirectFlightBuilder,
    FlightSearchBuilder
{
    /**
     * Create request with the direct flight defaults and translator
     *
     * @return RequestBuilder
     */
    public function directFlightRequest() : RequestBuilder
    {
        $translator = new DirectFlightRequestTranslator;

        $defaults = [
            'language' => 'en',
            'limit' => Config::get('providers.ryanair.direct_flight_item_limit'),
            'market' => 'en-gb',
            'offset' => 0,
            'dateFrom' => Carbon::now('UTC')->format('Y-m-d'),
            'dateTo' => Carbon::now('UTC')->addMonths(12)->format('Y-m-d'),
        ];

        $this->request = $this->createRequest($defaults, $translator);

        return $this;
    }

    /**
     * Create request with the flight search defaults and translator
     *
     * @return RequestBuilder
     */
    public function flightSearchRequest() : RequestBuilder
    {
        $translator = new SearchRequestTranslator;

        $defaults = [
            'ADT' => 1,
            'CHD' => 0,
            'INF' => 0,
            'TEEN' => 0,
            'ToUs' => 'AGREED',
            'dateFrom' => Carbon::now('UTC')->format('Y-m-d'),
            'exists' => 'false',
            'RoundTrip' => 'false',
            'IncludeConnectingFlights' => 'true'
        ];

        $this->request = $this->createRequest($defaults, $translator);

        return $this;
    }

    /**
     * Create request
     *
     * @param array             $defaults   Array of default values for request
     * @param RequestTranslator $translator Translator object
     *
     * @return void
     */
    protected function createRequest($defaults = [], $translator = null)
    {
        return new Request($defaults, $translator);
    }
}
