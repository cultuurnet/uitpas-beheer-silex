<?php

namespace CultuurNet\UiTPASBeheer\Exception;

class MockReadableCodeException extends MockResponseException implements ReadableCodeExceptionInterface
{
    /**
     * @var string
     */
    public $readableCode;

    /**
     * @param string $readableCode
     */
    public function setReadableCode($readableCode)
    {
        $this->readableCode = (string) $readableCode;
    }

    /**
     * @return string
     */
    public function getReadableCode()
    {
        return $this->readableCode;
    }
}
