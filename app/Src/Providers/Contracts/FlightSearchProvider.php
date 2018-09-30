<?php

namespace App\Src\Providers\Contracts;

use App\Src\FlightCollection;
use Carbon\Carbon;

interface FlightSearchProvider
{
    public function searchFlights(
        string $origin,
        string $destination,
        Carbon $date
    ) : FlightCollection;
}
