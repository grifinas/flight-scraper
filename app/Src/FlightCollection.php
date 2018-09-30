<?php

namespace App\Src;

use Illuminate\Support\Collection;

/**
 * Collection of flights returned by provider
 */
class FlightCollection
{
    /**
     * Collection of flights
     *
     * @var Collection
     */
    protected $data;

    /**
     * Total count of flights in API
     * This is used for pagination
     *
     * @var int
     */
    protected $total;

    /**
     * Construct the collection
     *
     * @param integer    $total Total amount of flights available on provider's side
     * @param Collection $data  Collection of flights
     */
    public function __construct(int $total, Collection $data)
    {
        $this->total = $total;
        $this->data = $data;
    }

    /**
     * Get the total count of flights on provider's side
     *
     * @return int
     */
    public function getTotal() : int
    {
        return $this->total;
    }

    /**
     * Get the underlyling collection of flights
     *
     * @return Collection
     */
    public function getCollection() : Collection
    {
        return $this->data;
    }

    /**
     * Add another flight to collection
     * The flight is assumed sepparate from those already in the collection,
     * thus total is incremented
     *
     * @param Flight $flight The flight to be added
     *
     * @return void
     */
    public function addFlight(Flight $flight)
    {
        $this->data->push($flight);
        $this->total++;
    }
}
