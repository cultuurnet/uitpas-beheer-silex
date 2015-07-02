<?php

namespace CultuurNet\UiTPASBeheer\Advantage\CashIn;

use CultuurNet\UiTPASBeheer\Exception\ReadableCodeExceptionInterface;
use CultuurNet\UiTPASBeheer\Exception\ResponseException;

class CashInContentTypeInvalidException extends ResponseException implements ReadableCodeExceptionInterface
{
    public function __construct($contentType)
    {
        $message = sprintf('%s is not a valid content-type for cashing in an advantage.', $contentType);
        parent::__construct($message, 400);
    }

    /**
     * @return string
     */
    public function getReadableCode()
    {
        return 'INVALID_ADVANTAGE_CASH_IN_CONTENT_TYPE';
    }
}
