<?php

namespace App\Src\Providers;

abstract class Provider
{
    /**
     * The request builder for a provider
     *
     * @var RequestBuilder
     */
    protected $requestBuilder;

    abstract public function __construct(
        RequestBuilder $requestBuilder = null
    );

    public function getRequestBuilder() : RequestBuilder
    {
        return $this->requestBuilder;
    }
}
