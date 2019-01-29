<?php

namespace App\Src\Providers\Ryanair;

use App\Src\FlightData as BaseFlightData;
use App\Src\Airport;
use App\Src\Money;
use Carbon\Carbon;

/**
 * Ryanair flight data
 */
class FlightData extends BaseFlightData
{
    const OUTBOUND_FARE = 1;
    const SEGMENT = 2;

    /**
     * Construct flight data from array of raw info and type
     *
     * @param array $data Raw data about the flight
     * @param int   $type Type specifier for data interpretation.
     *                    Should be FlightData constant
     */
    public function __construct(array $data, $type)
    {
        switch ($type) {
        case $this::OUTBOUND_FARE:
            return $this->setDataFromFareFormat($data);
        case $this::SEGMENT:
            return $this->setDataFromSegment($data);
        }
    }

    /**
     * Set flight data from segment information of a trip
     *
     * @param array $data Raw data
     *
     * @return void
     */
    public function setDataFromSegment(array $data)
    {
        $this->setDepartureAirport(new Airport(array_get($data, 'origin')));
        $this->setArrivalAirport(new Airport(array_get($data, 'destination')));

        $this->setDepartureDate(Carbon::parse(array_get($data, 'timeUTC.0')));
        $this->setArrivalDate(Carbon::parse(array_get($data, 'timeUTC.1')));

        $duration = Carbon::parse(array_get($data, 'duration'));
        
        $this->setDuration($duration->secondsSinceMidnight()/60);

        $this->setFlightNumber(array_get($data, 'flightNumber'));
    }

    /**
     * Set flight data from outbound fare information
     *
     * @param array $data Raw data
     *
     * @return void
     */
    public function setDataFromFareFormat(array $data)
    {
        $outbound = array_get($data, 'outbound');
        $this->setDepartureAirport(
            new Airport(
                array_get($outbound, 'departureAirport.iataCode'),
                array_get($outbound, 'departureAirport.name'),
                array_get($outbound, 'departureAirport.countryName')
            )
        );

        $this->setArrivalAirport(
            new Airport(
                array_get($outbound, 'arrivalAirport.iataCode'),
                array_get($outbound, 'arrivalAirport.name'),
                array_get($outbound, 'arrivalAirport.countryName')
            )
        );

        $this->setDepartureDate(Carbon::parse(array_get($outbound, 'departureDate')));
        $this->setArrivalDate(Carbon::parse(array_get($outbound, 'arrivalDate')));

        $this->setPrice(
            new Money(
                array_get($data, 'summary.price.value'),
                array_get($data, 'summary.price.currencyCode')
            )
        );
    }
}
