<?php

namespace CultuurNet\UiTPASBeheer\Advantage;

use CultuurNet\UiTPASBeheer\Exception\ReadableCodeExceptionInterface;
use CultuurNet\UiTPASBeheer\Exception\ReadableCodeResponseException;
use CultuurNet\UiTPASBeheer\Exception\ResponseException;
use Symfony\Component\HttpFoundation\Response;

class AdvantageNotFoundException extends ReadableCodeResponseException
{
    /**
     * @param AdvantageIdentifier $advantageIdentifier
     * @param int $code
     * @param \Exception $previous
     */
    public function __construct(
        AdvantageIdentifier $advantageIdentifier,
        $code = Response::HTTP_NOT_FOUND,
        $previous = null
    ) {
        $message = sprintf('The advantage with id %s was not found.', $advantageIdentifier->toNative());
        parent::__construct($message, 'ADVANTAGE_NOT_FOUND', $code, $previous);
    }
}
