<?php
/**
 * @file
 */

namespace CultuurNet\UiTPASBeheer\PassHolder\Properties;

use CultuurNet\UiTPASBeheer\Exception\CompleteResponseException;

class EmailAddressInvalidException extends CompleteResponseException
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
