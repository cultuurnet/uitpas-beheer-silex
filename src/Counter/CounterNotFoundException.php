<?php

namespace CultuurNet\UiTPASBeheer\Counter;

use CultuurNet\UiTPASBeheer\Exception\ReadableCodeExceptionInterface;
use CultuurNet\UiTPASBeheer\Exception\ResponseException;
use Symfony\Component\HttpFoundation\Response;

class CounterNotFoundException extends ResponseException implements ReadableCodeExceptionInterface
{
    /**
     * @param string $counterId
     * @param int $code
     * @param \Exception $previous
     */
    public function __construct($counterId, $code = Response::HTTP_NOT_FOUND, $previous = null)
    {
        $message = sprintf('The counter with id %s was not found.', $counterId);
        parent::__construct($message, $code, $previous);
    }

    /**
     * @return string
     */
    public function getReadableCode()
    {
        return 'COUNTER_NOT_FOUND';
    }
}
