<?php

namespace App\Src\Providers\Ryanair;

use App\Src\Providers\ProviderRequest;

/**
 * Ryanair request class for working with their APIs
 */
class Request extends ProviderRequest
{
    /**
     * Get all direct flights currently available
     *
     * @return array|null
     */
    public function getDirectFlights() : ?array
    {
        return $this->request(
            'GET',
            "https://api.ryanair.com/farefinder/3/oneWayFares",
            [
                'query' => $this->getParameters()
            ]
        );
    }

    /**
     * Get flights matchin the search criteria in the parameters
     *
     * @return array|null
     */
    public function getSearchedFlights() : ?array
    {
        return $this->request(
            'GET',
            "https://desktopapps.ryanair.com/v4/en-ie/availability",
            [
                'query' => $this->getParameters()
            ]
        );
    }

    /**
     * Get airport information
     *
     * @return array|null
     */
    public function getAirports() : ?array
    {
        return $this->request(
            'GET',
            "https://api.ryanair.com/aggregate/4/common?embedded=airports&market=en-gb"
        );
    }

    /**
     * @inheritDoc
     *
     * @return array|null
     */
    public function request($method, $url = '', array $options = []) : ?array
    {
        $response = parent::request($method, $url, $options);

        $body = $response->getBody()->getContents();

        return json_decode($body, true);
    }
}
