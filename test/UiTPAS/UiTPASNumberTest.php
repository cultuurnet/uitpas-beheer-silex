<?php

namespace CultuurNet\UiTPASBeheer\UiTPAS;

use CultuurNet\UiTPASBeheer\Exception\ReadableCodeExceptionInterface;
use CultuurNet\UiTPASBeheer\Exception\ResponseException;

class UiTPASNumberTest extends \PHPUnit_Framework_TestCase
{
    const VALID_REGULAR = '0930000420206';
    const VALID_KANSENSTATUUT = '0930000237915';

    const INVALID_START_DIGIT = '1930000420206';
    const INVALID_TOO_SHORT = '093000042020';
    const INVALID_TOO_LONG = '09300004202066';
    const INVALID_CHARACTERS = '09A3B004202066';
    const INVALID_CHECK_DIGIT = '0930000237912';
    const INVALID_KANSENSTATUUT = '0930000237931';

    /**
     * @test
     */
    public function it_can_return_the_number_without_kansenstatuut_and_check_digit()
    {
        $number = new UiTPASNumber(self::VALID_REGULAR);
        $this->assertTrue(0 === strcmp('09300004202', $number->getNumber()));
    }

    /**
     * @test
     */
    public function it_can_return_kansenstatuut_status()
    {
        $regular = new UiTPASNumber(self::VALID_REGULAR);
        $this->assertFalse($regular->hasKansenStatuut());

        $kansenStatuut = new UiTPASNumber(self::VALID_KANSENSTATUUT);
        $this->assertTrue($kansenStatuut->hasKansenStatuut());
    }

    /**
     * @test
     */
    public function it_can_return_the_check_digit()
    {
        $number = new UiTPASNumber(self::VALID_REGULAR);
        $this->assertTrue(6 === $number->getCheckDigit());
    }

    /**
     * @test
     */
    public function it_can_cast_back_to_original_string()
    {
        $number = new UiTPASNumber(self::VALID_REGULAR);

        $string = (string) $number;
        $this->assertTrue(self::VALID_REGULAR === $string);

        $native = $number->toNative();
        $this->assertTrue(self::VALID_REGULAR === $native);
    }

    /**
     * @test
     */
    public function it_validates_that_the_number_starts_with_zero()
    {
        $this->setExpectedException(
            UiTPASNumberInvalidException::class,
            'The provided value should be exactly 13 digits and start with 0.'
        );
        new UiTPASNumber(self::INVALID_START_DIGIT);
    }

    /**
     * @test
     */
    public function it_validates_that_the_number_is_not_shorter_than_13_digits()
    {
        $this->setExpectedException(
            UiTPASNumberInvalidException::class,
            'The provided value should be exactly 13 digits and start with 0.'
        );
        new UiTPASNumber(self::INVALID_TOO_SHORT);
    }

    /**
     * @test
     */
    public function it_validates_that_the_number_is_not_longer_than_13_digits()
    {
        $this->setExpectedException(
            UiTPASNumberInvalidException::class,
            'The provided value should be exactly 13 digits and start with 0.'
        );
        new UiTPASNumber(self::INVALID_TOO_LONG);
    }

    /**
     * @test
     */
    public function it_validates_that_the_number_contains_only_digits()
    {
        $this->setExpectedException(
            UiTPASNumberInvalidException::class,
            'The provided value should be exactly 13 digits and start with 0.'
        );
        new UiTPASNumber(self::INVALID_CHARACTERS);
    }

    /**
     * @test
     */
    public function it_validates_the_check_digit()
    {
        $this->setExpectedException(
            UiTPASNumberInvalidException::class,
            'The provided value does not have a valid check digit.'
        );
        new UiTPASNumber(self::INVALID_CHECK_DIGIT);
    }

    /**
     * @test
     */
    public function it_validates_the_kansenstatuut_digit()
    {
        $this->setExpectedException(
            UiTPASNumberInvalidException::class,
            'The second to last digit of the provided value should be 0 or 1.'
        );
        new UiTPASNumber(self::INVALID_KANSENSTATUUT);
    }

    /**
     * @test
     */
    public function it_returns_response_exceptions_with_a_readable_code()
    {
        try {
            new UiTPASNumber(self::INVALID_KANSENSTATUUT);
            $this->fail('UiTPASNumber should throw UiTPASNumberInvalidException when value is invalid.');
        } catch (UiTPASNumberInvalidException $exception) {
            $this->assertInstanceOf(ReadableCodeExceptionInterface::class, $exception);
            $this->assertEquals('UITPAS_NUMBER_INVALID', $exception->getReadableCode());

            $this->assertInstanceOf(ResponseException::class, $exception);
        }
    }
}
