<?php

namespace App\Src;

use Illuminate\Contracts\Support\Arrayable;

/**
 * Data representation of one flight to be used by flight providers
 */
class Flight implements Arrayable
{
    /**
     * Information related to this particular flight.
     * Stuff like airports, dates, duration is contained here
     *
     * @var FlightData
     */
    protected $data;

    /**
     * Construct a flight with some data
     *
     * @param FlightData $data The information about flight being created
     */
    public function __construct(FlightData $data)
    {
        $this->data = $data;
    }

    /**
     * Get the information about the flight
     *
     * @return FlightData
     */
    public function getData() : FlightData
    {
        return $this->data;
    }

    /**
     * Get the array representation of the flight
     *
     * @return array
     */
    public function toArray() : array
    {
        return $this->data->toArray();
    }
}
