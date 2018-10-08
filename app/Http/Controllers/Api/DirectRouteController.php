<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use App\Src\Providers\ProviderManager;
use App\Src\Providers\Provider;

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

                foreach ($providers as $i => $provider) {
                    if ($i > 0) {
                        echo ',';
                    }
                    $json = $provider->directFlights()->toJson();
                    
                    //echo json without the braces
                    echo substr($json, 1, -1);
                }

                echo ']';
            },
            200,
            //TODO headers
            []
        );
    }
}
