<?php

namespace App\Src\Providers\Contracts;

use Illuminate\Support\Collection;

interface DirectFlightProvider
{
    public function directFlights() : Collection;
}
