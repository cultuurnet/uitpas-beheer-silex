<?php

namespace CultuurNet\UiTPASBeheer\Exception;

class MissingPropertyException extends ReadableCodeResponseException
{
    public function __construct($parameter, $code = 'MISSING_PROPERTY')
    {
        $message = sprintf('Missing property "%s".', $parameter);
        parent::__construct($message, $code);
    }
}
