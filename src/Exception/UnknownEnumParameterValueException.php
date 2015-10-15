<?php

namespace CultuurNet\UiTPASBeheer\Exception;

class UnknownEnumParameterValueException extends CompleteResponseException
{
    private static function allowedValuesReducer($valueA, $valueB)
    {
        return $valueA ? $valueA . ', ' . $valueB : $valueB;
    }

    /**
     * @param string $parameter
     * @param string $enumClassName
     * @param string $code
     */
    public function __construct($parameter, $enumClassName, $code = 'UNKNOWN_ENUM_VALUE')
    {
        $enumReflectionClass = new \ReflectionClass($enumClassName);
        $allowedValues = $enumReflectionClass->getConstants();
        $concatenatedValues = array_reduce($allowedValues, array($this, 'allowedValuesReducer'));
        $message = sprintf('Unknown value for parameter "%s". The following values are allowed: %s', $parameter, $concatenatedValues);
        parent::__construct($message, $code);
    }
}
