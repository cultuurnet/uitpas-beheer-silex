<?php

namespace CultuurNet\UiTPASBeheer\DataValidation\Result;

use Guzzle\Http\Message\Response;

/**
 * Interface to implement for response handlers.
 */
interface ResponseToResultInterface
{
    /**
     * Parse the response of a request to a result.
     *
     * @param Response $response
     *   The response.
     * @return
     */
    public static function parseToResult(Response $response);
}
