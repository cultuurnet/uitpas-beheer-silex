<?php

namespace CultuurNet\UiTPASBeheer\DataValidation;

use CultuurNet\UiTPASBeheer\DataValidation\Item\RealtimeValidationResult;

/**
 * Defines the data validation client.
 */
interface DataValidationClientInterface
{
    /**
     * Real-time validate a given email address.
     *
     * @param $email
     * @return RealtimeValidationResult
     */
    public function realtimeValidateEmail($email);
}
