<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Src\ApiException;
use App\Src\Providers\ProviderManager;
use Illuminate\Support\Collection;
use Carbon\Carbon;
use App\Src\Providers\ProviderException;
use Illuminate\Support\Facades\Log;

class SearchRouteController
{
    public function __construct()
    {
        $this->providerManager = new ProviderManager;
    }

    public function postSearchResults(Request $request)
    {
        $content = json_decode($request->getContent(), true);

        $response = $this->validateSearches($content);
        
        $providers = $this->providerManager->generateSearchFlightProviders();

        foreach ($providers as $provider) {
            foreach ($response['valid'] as $identifier => $request) {
                try {
                    $flights = $provider->searchFlights(
                        $request['origin'],
                        $request['destination'],
                        Carbon::parse($request['date'])
                    );
    
                    $response[$identifier][] = $flights
                        ->getCollection()
                        ->toArray();
                } catch (ProviderException $e) {
                    Log::error(
                        $e->getMessage(),
                        [
                            'code' => $e->getCode(),
                            'request' => $content,
                        ]
                    );

                    $response['errors'][] = [
                        'identifier' => $identifier,
                        'code' => $e->getCode(),
                    ];
                    $response[$identifier] = null;
                }
            }
        }

        unset($response['valid']);

        return json_encode($response);
    }

    /**
     * Validate array of search requests grouping them into 'valid' and 'errors'
     *
     * @param array $content Search content from json
     *
     * @return array
     */
    private function validateSearches($content) : array
    {
        if (is_null($content) || !is_array($content)) {
            throw new ApiException("Wrong request", ApiException::WRONG_REQUEST);
        }

        $result = [
            'valid' => [],
            'errors' => [],
        ];

        foreach ($content as $searchRequest) {
            $id = array_get($searchRequest, 'identifier');

            if ($this->isValid($searchRequest)) {
                $result['valid'][$id] = $searchRequest;
            } elseif ($id) {
                $result['errors'][] = [
                    'identifier' => $id,
                    "code" => ApiException::WRONG_REQUEST
                ];
                $result[$id] = null;
            }
        }

        return $result;
    }

    /**
     * Check if search request is valid in structure
     *
     * @param array $searchRequest Search request data
     *
     * @return bool
     */
    private function isValid(array $searchRequest) : bool
    {
        $mandatoryFields = [
            'origin',
            'destination',
            'date',
            'identifier'
        ];

        foreach ($mandatoryFields as $field) {
            if (!isset($searchRequest[$field])) {
                return false;
            }
        }

        return true;
    }
}
