<?php
/**
 * @file
 */

namespace CultuurNet\UiTPASBeheer\PassHolder\Properties;

use CultuurNet\UiTPASBeheer\Exception\ReadableCodeResponseException;

class BirthDateInvalidException extends ReadableCodeResponseException
{
    /**
     * @param string $value
     */
    public function __construct($value)
    {
        $code = 'BIRTH_DATE_INVALID';
        $message = sprintf('Invalid birth date "%s".', $value);
        parent::__construct($message, $code);
    }
}
