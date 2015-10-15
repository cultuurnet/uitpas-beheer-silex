<?php
/**
 * @file
 */

namespace CultuurNet\UiTPASBeheer\Exception;

class UnknownParameterException extends CompleteResponseException
{
    public function __construct($parameter, $code = 'UNKNOWN_PARAMETER')
    {
        $message = sprintf('Unknown parameter "%s".', $parameter);
        parent::__construct($message, $code);
    }
}
