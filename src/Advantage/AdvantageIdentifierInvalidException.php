<?php

namespace CultuurNet\UiTPASBeheer\Advantage;

use CultuurNet\UiTPASBeheer\Exception\CompleteResponseException;

class AdvantageIdentifierInvalidException extends CompleteResponseException
{
    /**
     * @param string $message
     * @param int $code
     * @param \Exception $previous
     */
    public function __construct($message, $code = 400, \Exception $previous = null)
    {
        parent::__construct($message, 'ADVANTAGE_IDENTIFIER_INVALID', $code, $previous);
    }
}
