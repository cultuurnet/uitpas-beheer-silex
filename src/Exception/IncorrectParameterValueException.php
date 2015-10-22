<?php

namespace CultuurNet\UiTPASBeheer\Exception;

class IncorrectParameterValueException extends CompleteResponseException
{
    public function __construct($parameter, $code = 'INCORRECT_PARAMETER_VALUE', $context = null)
    {
        $message = sprintf('Incorrect value for parameter "%s".', $parameter);
        parent::__construct($message, $code);

        $this->setContext($context);
    }
}
