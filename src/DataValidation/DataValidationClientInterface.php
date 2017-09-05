<?php

namespace CultuurNet\UiTPASBeheer\DataValidation;

use CultuurNet\UiTPASBeheer\DataValidation\Item\EmailValidationResult;

/**
 * Defines the data validation client.
 */
interface DataValidationClientInterface
{
    /**
     * Real-time validate a given email address.
     *
     * @param $email
     * @return EmailValidationResult
     */
    public function validateEmail($email);
}
