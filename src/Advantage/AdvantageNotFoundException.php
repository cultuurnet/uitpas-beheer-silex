<?php

namespace CultuurNet\UiTPASBeheer\Advantage;

use CultuurNet\UiTPASBeheer\Exception\ReadableCodeExceptionInterface;
use CultuurNet\UiTPASBeheer\Exception\ResponseException;
use Symfony\Component\HttpFoundation\Response;

class AdvantageNotFoundException extends ResponseException implements ReadableCodeExceptionInterface
{
    public function __construct(
        AdvantageIdentifier $advantageIdentifier,
        $code = Response::HTTP_NOT_FOUND,
        $previous = null
    ) {
        parent::__construct('The advantage with id %s was not found.', $advantageIdentifier->toNative());
    }

    /**
     * @return string
     */
    public function getReadableCode()
    {
        return 'ADVANTAGE_NOT_FOUND';
    }
}
