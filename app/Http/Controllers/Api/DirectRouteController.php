<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use App\Src\Providers\ProviderManager;
use App\Src\Providers\Provider;

/**
 * @todo make work with multiple providers out of the box
 */
class DirectRouteController
{
    public function __construct()
    {
        $this->providerManager = new ProviderManager;
    }

    public function getIndex()
    {
        return response()->stream(
            function () {
                echo "[";

                $providers = $this->providerManager->generateDirectFlightProviders();
        
                foreach ($providers as $provider) {
                    $this->streamAllDirectFlights($provider);
                }
                echo ']';
            },
            200,
            //TODO headers
            []
        );
    }

    private function streamAllDirectFlights(Provider $provider, int $offset = 0, ?int $limit = null) : void
    {
        $flights = $provider->directFlights($offset, $limit);

        echo implode(',', $this->formDirectFlightJson($flights->getCollection()));

        flush();
        
        $offset += $flights->getCollection()->count();
        
        if ($flights->getTotal() > $offset) {
            echo ',';
            $this->streamAllDirectFlights($provider, $offset, $limit);
        }
    }

    private function formDirectFlightJson(Collection $flights) : array
    {
        $response = [];

        foreach ($flights as $flight) {
            $origin = $flight->getData()->getDepartureAirport();
            $destination = $flight->getData()->getArrivalAirport();
            $response[] = json_encode(
                [
                    'origin' => $origin->getCode(),
                    'destination' => $destination->getCode(),
                ]
            );
        }

        return $response;
    }
}
