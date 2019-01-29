<?php

namespace App\Src;

use Carbon\Carbon;
use Illuminate\Contracts\Support\Arrayable;

/**
 * Flight's information
 */
abstract class FlightData implements Arrayable
{
    /**
     * Airport that the flight starts in
     *
     * @var Airport
     */
    protected $departureAirport;

    /**
     * Date and time of liftoff
     *
     * @var Carbon
     */
    protected $departureDate;

    /**
     * Airport that the flight ends at
     *
     * @var Airport
     */
    protected $arrivalAirport;

    /**
     * Date and time of landing
     *
     * @var Carbon
     */
    protected $arrivalDate;

    /**
     * Price of the flight
     *
     * @var Money
     */
    protected $price;

    /**
     * Duration in minutes of the flight
     *
     * @var int
     */
    protected $duration;

    /**
     * Flight number
     *
     * @var string
     */
    protected $flightNumber;

    public function getDepartureAirport() : ?Airport
    {
        return $this->departureAirport;
    }

    public function getArrivalAirport() : ?Airport
    {
        return $this->arrivalAirport;
    }

    public function getDepartureDate() : ?Carbon
    {
        return $this->departureDate;
    }

    public function getArrivalDate() : ?Carbon
    {
        return $this->arrivalDate;
    }

    public function getPrice() : ?Money
    {
        return $this->price;
    }

    public function getFlightNumber() : ?string
    {
        return $this->flightNumber;
    }

    public function getDuration() : ?int
    {
        return $this->duration;
    }

    public function toArray()
    {
        /**
         * Uses the key is the call function to array of non null children
         * E.g. departureAirport->toArray(), departureDate->__toString()
         */
        $fieldsActions = [
            '' => [
                'flightNumber' => $this->getFlightNumber(),
                'duration' => $this->getDuration(),
            ],
            'getCode' => [
                'departureAirport' => $this->getDepartureAirport(),
                'arrivalAirport' => $this->getArrivalAirport(),
            ],
            'toArray' => [
                'price' => $this->getPrice(),
            ],
            '__toString' => [
                'departureDate' => $this->getDepartureDate(),
                'arrivalDate' => $this->getArrivalDate(),
            ],
        ];

        $response = [];
        foreach ($fieldsActions as $action => $fields) {
            foreach ($fields as $key => $value) {
                if (is_null($value)) {
                    continue;
                }

                $response[$key] = $action ? $value->$action() : $value;
            }
        }

        return $response;
    }

    protected function setDepartureAirport(Airport $airport)
    {
        $this->departureAirport = $airport;
    }

    protected function setArrivalAirport(Airport $airport)
    {
        $this->arrivalAirport = $airport;
    }

    protected function setDepartureDate(Carbon $date)
    {
        $this->departureDate = $date;
    }

    protected function setArrivalDate(Carbon $date)
    {
        $this->arrivalDate = $date;
    }

    protected function setPrice(Money $price)
    {
        $this->price = $price;
    }

    protected function setDuration(int $duration)
    {
        $this->duration = $duration;
    }

    protected function setFlightNumber($flightNumber)
    {
        $this->flightNumber = $flightNumber;
    }
}
