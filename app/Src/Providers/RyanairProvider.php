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
    public function directFlights(
        int $offset = 0,
        ?int $limit = null
    ) : FlightCollection {
        $builder = $this->getRequestBuilder();

        $builder->directFlightRequest()
            ->set('offset', $offset);

        if ($limit) {
            $builder->set('limit', $limit);
        }

        $request = $builder->build();

        $hash = md5(implode(',', $request->getParameters()));

        return Cache::remember(
            "ryanair_direct_flights:$hash",
            Config::get('providers.ryanair.direct_flight_cache_minutes'),
            function () use ($request) {
                $response = $request->getDirectFlights();

                $flightCollection = new Collection;
            
                //TODO Don't cache this
                if (!$response) {
                    return new FlightCollection(0, $flightCollection);
                }

                foreach ($response['fares'] as $fare) {
                    $flightCollection->push(
                        new Flight(new FlightData($fare, FlightData::OUTBOUND_FARE))
                    );
                }
    
                return new FlightCollection($response['total'], $flightCollection);
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
     * @return FlightCollection
     */
    public function searchFlights(
        string $origin,
        string $destination,
        Carbon $date
    ) : FlightCollection {
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

        $flightCollection = new FlightCollection(0, new Collection());
            
        if (!$response) {
            return $flightCollection;
        }

        $trips = array_get($response, 'trips', []);

        foreach ($trips as $trip) {
            //Api supports leeway for departure date. We don't, so take the first date
            $flights = array_get($trip, 'dates.0.flights', []);

            if (empty($flights)) {
                throw new ProviderException("No flights", ProviderException::NOT_FOUND);
            }

            foreach ($flights as $flight) {
                $flightCollection->addFlight(new Flight(new TripData($trip, $flight)));
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
