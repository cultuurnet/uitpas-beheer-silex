<?php

namespace CultuurNet\UiTPASBeheer\Counter;

use ValueObjects\StringLiteral\StringLiteral;

class CounterConsumerKey extends StringLiteral
{
    /**
     * @param string $value
     *
     * @throws \InvalidArgumentException
     *   When the provided value is empty.
     */
    public function __construct($value)
    {
        // Don't do any other validation, as there's no single definitive format for counter consumer keys.
        // There are keys with the format 8-4-4-16, keys without dashes, etc etc.
        if (empty($value)) {
            throw new \InvalidArgumentException('Counter consumer key should not be empty.');
        }

        parent::__construct($value);
    }
}
