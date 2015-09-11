<?php
/**
 * @file
 */

namespace CultuurNet\UiTPASBeheer\PassHolder\Properties;

use CultuurNet\UiTPASBeheer\Exception\ReadableCodeResponseException;

class EmailAddressInvalidException extends ReadableCodeResponseException
{
    /**
     * @param string $value
     */
    public function __construct($value)
    {
        $code = 'EMAIL_ADDRESS_INVALID';
        $message = sprintf('Invalid email address "%s".', $value);
        parent::__construct($message, $code);
    }
}
