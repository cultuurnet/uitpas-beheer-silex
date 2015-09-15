<?php

namespace CultuurNet\UiTPASBeheer\KansenStatuut;

use CultuurNet\UiTPASBeheer\Exception\ReadableCodeResponseException;

class KansenStatuutEndDateInvalidException extends ReadableCodeResponseException
{
    /**
     * @param string $value
     */
    public function __construct($value)
    {
        $code = 'KANSENSTATUUT_END_DATE_INVALID';
        $message = sprintf('Invalid kansenstatuut end date "%s".', $value);
        parent::__construct($message, $code);
    }
}
