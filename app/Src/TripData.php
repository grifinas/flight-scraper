<?php

namespace App\Src;

use Carbon\Carbon;
use Illuminate\Contracts\Support\Arrayable;

/**
 * Representation of trip data.
 * Trip is considered a form of flight containing other flights
 */
abstract class TripData extends FlightData implements Arrayable
{
    protected $segments;
    protected $prices;

    /**
     * Add a flight segment to the trip
     *
     * @param Flight $flight The flight we're adding
     *
     * @return void
     */
    public function addSegment(Flight $flight)
    {
        $this->segments[] = $flight;
    }

    /**
     * Add a price listing to the trip
     *
     * @param string $type  The type of price this is (Adult/Child/Elderly etc.)
     * @param Money  $price The price object
     *
     * @return void
     */
    public function addPrice(string $type, Money $price)
    {
        $this->prices[$type] = $price;
    }

    /**
     * Get array representation of trip
     *
     * @return array
     */
    public function toArray() : array
    {
        $response = parent::toArray();

        foreach ($this->segments as $flight) {
            $response['segments'][] = $flight->toArray();
        }

        foreach ($this->prices as $type => $price) {
            $price = $price->toArray();
            $price['paxType'] = $type;
            $response['price'][] = $price;
        }

        return $response;
    }

    /**
     * Trip doesn't have a singular price and thus doesn't support getting it
     *
     * @return null
     */
    public function getPrice(): ?Money
    {
        return null;
    }
}
