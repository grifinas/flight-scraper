<?php

namespace App\Src;

use Illuminate\Contracts\Support\Arrayable;

/**
 * Data representation of the airport made for use by flight providers
 */
class Airport implements Arrayable
{
    /**
     * IATA code of the airport
     *
     * @var string
     */
    protected $iataCode;

    /**
     * Full name of the airport
     *
     * @var string
     */
    protected $name;

    /**
     * Country of the airport
     *
     * @var string
     */
    protected $country;

    /**
     * Construct an airport using it's basic info
     *
     * @param string $code    IATA code of airport
     * @param string $name    Name of an airport
     * @param string $country Country of the airport
     */
    public function __construct(
        string $code,
        string $name = null,
        string $country = null
    ) {
        $this->iataCode = $code;
        $this->name = $name;
        $this->country = $country;
    }

    /**
     * Get the IATA code of the airport
     *
     * @return string The IATA code of the airport
     */
    public function getCode() : string
    {
        return $this->iataCode;
    }

    /**
     * Get the array representation of the airport
     *
     * @return array Airport data
     */
    public function toArray() : array
    {
        $response = [
            'iataCode' => $this->iataCode,
        ];

        if ($this->name) {
            $response['name'] = $this->name;
        }

        if ($this->country) {
            $response['country'] = $this->country;
        }

        return $response;
    }
}
