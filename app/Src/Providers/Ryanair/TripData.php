<?php

namespace App\Src\Providers\Ryanair;

use App\Src\TripData as BaseTripData;
use App\Src\Flight;
use App\Src\Airport;
use App\Src\Money;
use App\Src\Providers\ProviderException;

/**
 * Ryanair trip data
 */
class TripData extends BaseTripData
{
    /**
     * Constructs a trip form raw data
     *
     * @param array $data Raw data
     */
    public function __construct(array $data, array $flight)
    {
        $this->setDepartureAirport(
            new Airport(
                array_get($data, 'origin'),
                array_get($data, 'originName')
            )
        );

        $this->setArrivalAirport(
            new Airport(
                array_get($data, 'destination'),
                array_get($data, 'destinationName')
            )
        );
        
        if ($flight['faresLeft'] < 1) {
            throw new ProviderException(
                "No fares left",
                ProviderException::SOLD_OUT
            );
        }
        
        foreach (array_get($flight, 'segments', []) as $segment) {
            $this->addSegment(
                new Flight(new FlightData($segment, FlightData::SEGMENT))
            );
        }

        foreach (array_get($flight, 'regularFare.fares', []) as $fare) {
            $this->addPrice($fare['type'], new Money($fare['amount'], 'EUR'));
        }

        $lastFlight = end($this->segments);
        $firstFlight = reset($this->segments);

        $departure = $firstFlight->getData()->getDepartureDate();
        $arrival = $lastFlight->getData()->getArrivalDate();

        $duration = $departure->diffInMinutes($arrival);

        $this->setDepartureDate($departure);
        $this->setArrivalDate($arrival);

        $this->setDuration($duration);
    }
}
