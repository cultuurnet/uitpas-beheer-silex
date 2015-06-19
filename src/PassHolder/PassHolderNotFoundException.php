<?php

namespace CultuurNet\UiTPASBeheer\PassHolder;

use CultuurNet\UiTPASBeheer\Exception\ReadableCodeExceptionInterface;
use CultuurNet\UiTPASBeheer\Exception\ResponseException;

class PassHolderNotFoundException extends ResponseException implements ReadableCodeExceptionInterface
{
    /**
     * @param int $code
     * @param \Exception $previous
     */
    public function __construct($code = 404, \Exception $previous = null)
    {
        $message = 'No passholder found with this identification.';
        parent::__construct($message, $code, $previous);
    }

    /**
     * @return string
     */
    public static function getReadableCode()
    {
        return 'PASSHOLDER_NOT_FOUND';
    }
}
