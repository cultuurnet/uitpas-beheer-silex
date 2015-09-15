<?php

namespace CultuurNet\UiTPASBeheer\PassHolder;

use CultuurNet\UiTPASBeheer\Exception\ReadableCodeResponseException;

class UiTPASNumberAlreadyUsedException extends ReadableCodeResponseException
{
    public function __construct()
    {
        parent::__construct(
            'This UiTPAS number is already in use',
            'UITPASNUMBER_ALREADY_USED',
            400
        );
    }
}
