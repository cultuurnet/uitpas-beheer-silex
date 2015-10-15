<?php

namespace CultuurNet\UiTPASBeheer\Activity;

use CultuurNet\UiTPASBeheer\Exception\CompleteResponseException;

class DateTypeInvalidException extends CompleteResponseException
{
    /**
     * @param string $value
     */
    public function __construct($value)
    {
        $code = 'DATE_TYPE_INVALID';
        $message = sprintf('Invalid date type "%s".', $value);
        parent::__construct($message, $code);
    }
}
