<?php

namespace App\Src\Providers;

use App\Src\Providers\Contracts\DirectFlightProvider;
use App\Src\Flight;
use App\Src\Providers\Ryanair\FlightData;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use App\Src\FlightCollection;
use App\Src\Providers\Ryanair\RequestBuilder;
use App\Src\Providers\RequestBuilder as AnyRequestBuilder;
use App\Src\Providers\Contracts\FlightSearchProvider;
use App\Src\Providers\Ryanair\TripData;
use Carbon\Carbon;
use App\Src\Providers\ProviderException;

/**
 * The Ryanair flight provider
 */
class RyanairProvider extends Provider implements
    DirectFlightProvider,
    FlightSearchProvider
{
    /**
     * Construct the provider taking in the request builder
     * If no builder is passed default is used
     *
     * @param AnyRequestBuilder $requestBuilder Request builder for making requests
     */
    public function __construct(
        AnyRequestBuilder $requestBuilder = null
    ) {
        $this->requestBuilder = $requestBuilder ?? new RequestBuilder;
    }

    /**
     * Get all direct flights that this provider is providing
     *
     * @param integer      $offset Flight offset from the start
     * @param integer|null $limit  How many flights to return in one go
     *
     * @return FlightCollection
     */
    public function directFlights() : Collection
    {
        return Cache::remember(
            "ryanair_direct_flights",
            Config::get('providers.ryanair.direct_flight_cache_minutes'),
            function () {
                $allRoutes = [];
                foreach ($this->allAirports() as $airport) {
                    $origin = $airport['iataCode'];

                    foreach (array_get($airport, 'routes', []) as $route) {
                        if (starts_with($route, 'airport:')) {
                            $destination = substr($route, 8, 3);

                            $allRoutes[] = [
                                'origin' => $origin,
                                'destination' => $destination
                            ];
                        }
                    }
                }

                return collect($allRoutes);
            }
        );
    }

    /**
     * Search for a specific travel route
     *
     * @param string $origin      IATA code of the airport we're leaving from
     * @param string $destination IATA code of our destination
     * @param Carbon $date        Date and time we're looking to depart at
     *
     * @return Collection
     */
    public function searchFlights(
        string $origin,
        string $destination,
        Carbon $date
    ) : Collection {
        if (!$this->routeExists($origin, $destination)) {
            throw new ProviderException(
                "Route not found",
                ProviderException::NO_SUCH_ROUTE
            );
        }

        $request = $this->getRequestBuilder()->flightSearchRequest()
            ->set('origin', $origin)
            ->set('destination', $destination)
            ->set('dateFrom', $date->format('Y-m-d'))
            ->build();

        $response = $request->getSearchedFlights();

        $flightCollection = new Collection();

        if (!$response) {
            return $flightCollection;
        }

        $trips = array_get($response, 'trips', []);

        foreach ($trips as $trip) {
            //Api supports leeway for departure date.
            //We don't, so take the first date
            $flights = array_get($trip, 'dates.0.flights', []);

            if (empty($flights)) {
                throw new ProviderException(
                    "No flights",
                    ProviderException::NOT_FOUND
                );
            }

            foreach ($flights as $flight) {
                $flightCollection->push(
                    new Flight(new TripData($trip, $flight))
                );
            }
        }

        return $flightCollection;
    }

    /**
     * Check if route exists between two airports
     *
     * @param string $origin      Origin airport IATA code
     * @param string $destination Destination airport IATA code
     *
     * @return bool
     */
    private function routeExists(string $origin, string $destination) : bool
    {
        $airport = $this->airport($origin);

        $needle = "airport:$destination";
        foreach ($airport['routes'] as $route) {
            if ($route == $needle) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get the information of an airport
     *
     * @param string $airportCode IATA code of an airport
     *
     * @return array
     */
    private function airport(string $airportCode) : array
    {
        $all = $this->allAirports();

        foreach ($all as $airport) {
            if ($airport['iataCode'] == $airportCode) {
                return $airport;
            }
        }

        return [];
    }

    /**
     * API call to get the info of all airports and their routes
     *
     * @return array|null
     */
    private function allAirports() : ?array
    {
        return Cache::remember(
            'ryanair_airport_routes',
            Config::get('providers.ryanair.all_airports_cache_minutes'),
            function () {
                $request = $this->getRequestBuilder()->default()->build();

                $response = $request->getAirports();

                return array_get($response, 'airports');
            }
        );
    }
}
