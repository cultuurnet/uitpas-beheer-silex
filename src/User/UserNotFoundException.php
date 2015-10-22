<?php

namespace CultuurNet\UiTPASBeheer\User;

use CultuurNet\UiTPASBeheer\Exception\CompleteResponseException;

class UserNotFoundException extends CompleteResponseException
{
    public function __construct($searchProperty, $value)
    {
        parent::__construct(
            sprintf(
                'No user found with %s "%s".',
                $searchProperty,
                $value
            ),
            'USER_NOT_FOUND',
            404
        );
    }
}
