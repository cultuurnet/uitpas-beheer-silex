<?php

namespace CultuurNet\UiTPASBeheer\UiTPAS;

use PayBreak\Luhn\Luhn;
use ValueObjects\StringLiteral\StringLiteral;

/**
 * @see Functional description UiTPAS system.
 */
class UiTPASNumber extends StringLiteral
{
    /**
     * An UiTPAS number is always exactly 13 digits and start with 0.
     */
    const FORMAT = '^\d{13}$';

    /**
     * Contains prefix and serial number.
     *
     * We don't have a fixed list of prefixes and there's no separator.
     * Should be a string because it should always start with a 0.
     *
     * @var string
     */
    protected $number;

    /**
     * @var int
     */
    protected $kansenStatuutDigit;

    /**
     * @var int
     */
    protected $checkDigit;

    /**
     * @param string $value
     *
     * @throws UiTPASNumberInvalidException
     *   When the provided value is not a string of exactly 13 digits.
     *   When the provided value is not valid according to the Luhn algorithm.
     *   When the provided value's second to last digit (kansenstatuut) is not 0 or 1.
     */
    public function __construct($value)
    {
        // Validate length and contents of the string.
        if (!preg_match('/' . self::FORMAT . '/', $value)) {
            throw new UiTPASNumberInvalidException('The provided value should be exactly 13 digits.');
        }

        // Make sure the number is valid according to the Luhn algorithm.
        if (!Luhn::validateNumber($value)) {
            throw new UiTPASNumberInvalidException('The provided value does not have a valid check digit.');
        }

        // The number consists of all digits except for the last two.
        $this->number = substr($value, 0, -2);

        // Kansen statuut digit is the second last digit.
        $this->kansenStatuutDigit = (int) substr($value, -2, 1);
        if (!in_array($this->kansenStatuutDigit, [0, 1])) {
            throw new UiTPASNumberInvalidException('The second to last digit of the provided value should be 0 or 1.');
        }

        // Check digit is the last digit.
        $this->checkDigit = (int) substr($value, -1);

        // Finally store the value.
        parent::__construct($value);
    }

    /**
     * @return bool
     */
    public function hasKansenStatuut()
    {
        return (bool) $this->kansenStatuutDigit;
    }

    /**
     * @return int
     */
    public function getCheckDigit()
    {
        return $this->checkDigit;
    }
}
