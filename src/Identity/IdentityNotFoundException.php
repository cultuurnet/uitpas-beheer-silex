<?php

namespace CultuurNet\UiTPASBeheer\Identity;

use CultuurNet\UiTPASBeheer\Exception\ReadableCodeExceptionInterface;
use CultuurNet\UiTPASBeheer\Exception\CompleteResponseException;
use CultuurNet\UiTPASBeheer\Exception\ResponseException;

class IdentityNotFoundException extends CompleteResponseException
{
    /**
     * @param int $code
     * @param \Exception $previous
     */
    public function __construct($code = 404, \Exception $previous = null)
    {
        parent::__construct(
            'No identity could be found with this identification.',
            'IDENTITY_NOT_FOUND',
            $code,
            $previous
        );
    }
}
