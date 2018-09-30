<?php

namespace App\Src;

use Exception;

/**
 * General Api exception to be used in controllers under API folder.
 */
class ApiException extends Exception
{
    const WRONG_REQUEST = 20;
    const UNSPECIFIED = 90;
    
    /**
     * Construct an Api exception.
     * If no error code is specified the the unspecified constant is used
     *
     * @param string    $message  Message string of the error
     * @param integer   $code     Error code
     * @param Exception $previous Previous exception, if nested
     */
    public function __construct(
        $message = null,
        $code = 0,
        Exception $previous = null
    ) {
        if ($code === 0) {
            $code = $this::UNSPECIFIED;
        }
        parent::__construct($message, $code, $previous);
    }
}
