<?php

namespace App\Src\Providers\Contracts;

use Carbon\Carbon;
use Illuminate\Support\Collection;

interface FlightSearchProvider
{
    public function searchFlights(
        string $origin,
        string $destination,
        Carbon $date
    ) : Collection;
}
