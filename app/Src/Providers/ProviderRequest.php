<?php

namespace App\Src\Providers;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

/**
 * Wrapper for guzzle client to be used by providers
 */
class ProviderRequest extends Client
{
    /**
     * Parameters that are specific to the request
     *
     * @var array
     */
    protected $parameters;

    /**
     * Translator to be used when getting parameters
     *
     * @var null|RequestTranslator
     */
    protected $translator;

    /**
     * Construct a request from initial parameters and translator
     *
     * @param array             $parameters Initial parameters of the request
     * @param RequestTranslator $translator Translator to be used
     */
    public function __construct(array $parameters = [], RequestTranslator $translator = null)
    {
        $this->parameters = $parameters;
        $this->translator = $translator;

        parent::__construct();
    }

    /**
     * Get translated parameters
     *
     * @return array
     */
    public function getParameters() : array
    {
        if ($this->translator) {
            return $this->translator->translate($this);
        }

        return $this->getUntranslatedParameters();
    }

    /**
     * Get untranslated parameters
     *
     * @return array
     */
    public function getUntranslatedParameters() : array
    {
        return $this->parameters;
    }

    /**
     * Parameter setter
     *
     * @param string $key   Parameter key
     * @param mixed  $value Parameter value
     *
     * @return void
     */
    public function setParameter($key, $value)
    {
        $this->parameters[$key] = $value;
    }

    /**
     * Request provider api and return the response
     *
     * @param string $method  Method of request should be GET or POST
     * @param string $url     Url to request to
     * @param array  $options Optional parameters, query string, headers
     *
     * @return mixed Response in some provider specific way
     */
    public function request($method, $url = '', array $options = [])
    {
        $start = microtime(true);

        $response = parent::request($method, $url, $options);

        Log::info(
            "$method request to: $url in " . (microtime(true) - $start),
            $options
        );

        return $response;
    }
}
