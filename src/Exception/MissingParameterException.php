<?php

namespace CultuurNet\UiTPASBeheer\Exception;

class MissingParameterException extends ReadableCodeResponseException
{
    public function __construct($parameter, $code = 'MISSING_PARAMETER')
    {
        $message = sprintf('Missing parameter "%s".', $parameter);
        parent::__construct($message, $code);
    }
}
