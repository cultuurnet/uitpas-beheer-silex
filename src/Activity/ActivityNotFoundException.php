<?php

namespace CultuurNet\UiTPASBeheer\Activity;

use CultuurNet\UiTPASBeheer\Exception\ReadableCodeExceptionInterface;
use CultuurNet\UiTPASBeheer\Exception\ReadableCodeResponseException;
use CultuurNet\UiTPASBeheer\Exception\ResponseException;
use Symfony\Component\HttpFoundation\Response;
use CultuurNet\UiTPASBeheer\Activity\Cdbid;

class ActivityNotFoundException extends ReadableCodeResponseException
{
    /**
     * @param Cdbid $eventCdbid
     * @param int $code
     * @param \Exception $previous
     */
    public function __construct(
        Cdbid $eventCdbid,
        $code = Response::HTTP_NOT_FOUND,
        $previous = null
    ) {
        $message = sprintf('The activity with cdbid %s was not found.', $eventCdbid->toNative());
        parent::__construct($message, 'ACTIVITY_NOT_FOUND', $code, $previous);
    }
}
