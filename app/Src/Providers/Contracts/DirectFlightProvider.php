<?php

namespace App\Src\Providers\Contracts;

use App\Src\FlightCollection;

interface DirectFlightProvider
{
    public function directFlights(int $offset = 0, ?int $limit = null) : FlightCollection;
}
