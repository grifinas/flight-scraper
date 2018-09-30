<?php

namespace App\Src\Providers;

/**
 * Abstract request builder to be extended by providers
 */
abstract class RequestBuilder
{
    /**
     * Request that we're building
     *
     * @var ProviderRequest
     */
    protected $request;

    /**
     * Every provider needs to have a function to create a request
     * This is used for default build and should not have to pass any parameters
     *
     * @return void
     */
    abstract protected function createRequest();

    /**
     * Finish building the request and return it
     *
     * @return ProviderRequest
     */
    public function build() : ProviderRequest
    {
        $request = $this->request;
        unset($this->request);
        return $request;
    }

    /**
     * Set some attribute of the request
     *
     * @param string $key   Key value of the parameter
     * @param mixed  $value Value of the parameter
     *
     * @return RequestBuilder
     */
    public function set($key, $value) : RequestBuilder
    {
        $this->request->setParameter($key, $value);
        return $this;
    }

    /**
     * Default configuration for request with no default parameters
     *
     * @return RequestBuilder
     */
    public function default() : RequestBuilder
    {
        $this->request = $this->createRequest();
        return $this;
    }
}
