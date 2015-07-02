<?php

namespace CultuurNet\UiTPASBeheer\Exception;

class InternalErrorException extends ResponseException implements ReadableCodeExceptionInterface
{
    public function __construct()
    {
        parent::__construct('An internal error occurred.', 500);
    }

    /**
     * @return string
     */
    public function getReadableCode()
    {
        return 'INTERNAL_ERROR';
    }
}
