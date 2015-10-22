<?php

namespace CultuurNet\UiTPASBeheer\PassHolder;

use CultuurNet\UiTPASBeheer\Exception\CompleteResponseException;

class UiTPASNumberAlreadyUsedException extends CompleteResponseException
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
