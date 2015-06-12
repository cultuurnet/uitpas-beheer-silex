<?php

namespace CultuurNet\UiTPASBeheer\Exception;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

/**
 * Used to catch multiple exceptions that are allowed to be detailed
 * in the response at once.
 *
 * Otherwise we'd have to catch all exceptions by catching \Exception,
 * which could potentially reveal internal security issues when returning
 * unforeseen exceptions with JsonErrorResponse.
 */
abstract class ResponseException extends \Exception implements HttpExceptionInterface {
    /**
     * Returns the status code.
     *
     * @return int An HTTP response status code
     */
    public function getStatusCode()
    {
        return $this->getCode();
    }

    /**
     * Returns response headers.
     *
     * @return array Response headers
     */
    public function getHeaders()
    {
        return array();
    }
}
