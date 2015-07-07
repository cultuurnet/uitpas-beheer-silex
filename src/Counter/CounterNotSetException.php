<?php

namespace CultuurNet\UiTPASBeheer\Counter;

use CultuurNet\UiTPASBeheer\Exception\ReadableCodeExceptionInterface;
use CultuurNet\UiTPASBeheer\Exception\ResponseException;
use Symfony\Component\HttpFoundation\Response;

class CounterNotSetException extends ResponseException implements ReadableCodeExceptionInterface
{
    /**
     * @param int $code
     * @param \Exception $previous
     */
    public function __construct($code = Response::HTTP_NOT_FOUND, \Exception $previous = null)
    {
        $message = 'No active counter set for the current user.';
        parent::__construct($message, $code, $previous);
    }

    /**
     * @return string
     */
    public function getReadableCode()
    {
        return 'COUNTER_NOT_SET';
    }
}
