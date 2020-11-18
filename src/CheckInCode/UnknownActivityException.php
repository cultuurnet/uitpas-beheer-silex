<?php

namespace CultuurNet\UiTPASBeheer\CheckInCode;

use CultuurNet\UiTPASBeheer\Exception\ResponseException;

class UnknownActivityException extends ResponseException
{
    /**
     * @param int $code
     * @param \Exception $previous
     */
    public function __construct($code = 404, \Exception $previous = null)
    {
        $message = 'Unknown activity id.';
        parent::__construct($message, $code, $previous);
    }

    /**
     * @return string
     */
    public function getReadableCode()
    {
        return 'UNKNOWN_ACTIVITY';
    }
}
