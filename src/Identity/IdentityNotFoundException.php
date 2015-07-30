<?php

namespace CultuurNet\UiTPASBeheer\Identity;

use CultuurNet\UiTPASBeheer\Exception\ReadableCodeExceptionInterface;
use CultuurNet\UiTPASBeheer\Exception\ResponseException;

class IdentityNotFoundException extends ResponseException implements ReadableCodeExceptionInterface
{
    /**
     * @param int $code
     * @param \Exception $previous
     */
    public function __construct($code = 404, \Exception $previous = null)
    {
        $message = 'No identity found with this identification.';
        parent::__construct($message, $code, $previous);
    }

    /**
     * @return string
     */
    public function getReadableCode()
    {
        return 'IDENTITY_NOT_FOUND';
    }
}
