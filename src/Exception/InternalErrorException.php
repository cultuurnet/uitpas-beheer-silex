<?php

namespace CultuurNet\UiTPASBeheer\Exception;

class InternalErrorException extends CompleteResponseException
{
    public function __construct()
    {
        parent::__construct('An internal error occurred.', 'INTERNAL_ERROR', 500);
    }
}
