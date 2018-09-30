<?php

namespace App\Src\Providers;

use App\Src\Providers\ProviderRequest;

/**
 * Base translator class for providers to extend
 */
abstract class RequestTranslator
{
    /**
     * Array of key value pairs that this translator can translate
     * All keys not in the list are left as is
     * The key is the current version, value is the intended version
     *
     * @var array
     */
    protected $keys;

    public function translate(ProviderRequest $data) : array
    {
        $attrs = $data->getUntranslatedParameters();

        foreach ($this->keys as $old => $new) {
            if (isset($attrs[$old])) {
                $attrs[$new] = $attrs[$old];
                unset($attrs[$old]);
            }
        }

        return $attrs;
    }
}
