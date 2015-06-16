<?php

namespace CultuurNet\UiTPASBeheer\Exception;

class MockReadableCodeException extends MockResponseException implements ReadableCodeExceptionInterface
{
    /**
     * @var string
     */
    public static $readableCode;

    public static function setReadableCode($readableCode)
    {
        self::$readableCode = (string) $readableCode;
    }

    /**
     * @return string
     */
    public static function getReadableCode()
    {
        return (string) self::$readableCode;
    }
}
