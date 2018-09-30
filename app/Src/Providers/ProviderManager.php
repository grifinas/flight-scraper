<?php

namespace App\Src\Providers;

use Illuminate\Support\Collection;
use App\Src\Providers\Contracts\DirectFlightProvider;
use App\Src\Providers\Contracts\FlightSearchProvider;

/**
 * Manager class to easily work with multiple providers
 */
class ProviderManager
{
    protected $providers;

    /**
     * Create provider manager.
     * Who in itself creates all the necesary providers
     *
     * @todo Lazy load the providers
     */
    public function __construct()
    {
        $this->providers = new Collection($this->getProviderArray());
    }

    /**
     * Get the collection of all providers
     *
     * @return Collection
     */
    public function getProviders() : Collection
    {
        return $this->providers;
    }

    /**
     * Get the generator to all providers implementing DirectFlightProvider
     *
     * @return void
     */
    public function generateDirectFlightProviders()
    {
        foreach ($this->getProviders() as $provider) {
            if ($provider instanceof DirectFlightProvider) {
                yield $provider;
            }
        }
    }

    /**
     * Get the generator to all providers implementing FlightSearchProvider
     *
     * @return void
     */
    public function generateSearchFlightProviders()
    {
        foreach ($this->getProviders() as $provider) {
            if ($provider instanceof FlightSearchProvider) {
                yield $provider;
            }
        }
    }

    /**
     * Return array of all providers that are used in the system
     *
     * @return array
     */
    protected function getProviderArray() : array
    {
        return [
            new RyanairProvider,
        ];
    }
}
