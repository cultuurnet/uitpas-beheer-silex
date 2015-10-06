<?php

namespace CultuurNet\UiTPASBeheer\Properties;

use CultuurNet\UiTPASBeheer\Exception\IncorrectParameterValueException;
use CultuurNet\UiTPASBeheer\Exception\MissingPropertyException;
use ValueObjects\DateTime\Date;
use ValueObjects\DateTime\Month;
use ValueObjects\DateTime\MonthDay;
use ValueObjects\DateTime\Year;
use ValueObjects\StringLiteral\StringLiteral;

class DateRangeJsonDeserializerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var DateRangeJsonDeserializer
     */
    protected $deserializer;

    public function setUp()
    {
        $this->deserializer = new DateRangeJsonDeserializer();
    }

    /**
     * @test
     */
    public function it_deserializes_a_valid_date_range()
    {
        $expected = new DateRange(
            new Date(
                new Year('2015'),
                Month::get('October'),
                new MonthDay('1')
            ),
            new Date(
                new Year('2016'),
                Month::get('November'),
                new MonthDay('30')
            )
        );

        $json = file_get_contents(__DIR__ . '/data/date-range.json');

        $actual = $this->deserializer->deserialize(
            new StringLiteral($json)
        );

        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     * @dataProvider requiredPropertyDataProvider
     *
     * @param string $invalidJson
     * @param string $missingProperty
     */
    public function it_throws_an_exception_when_a_required_property_is_missing(
        $invalidJson,
        $missingProperty
    ) {
        $this->setExpectedException(
            MissingPropertyException::class,
            sprintf('Missing property "%s".', $missingProperty)
        );
        $this->deserializer->deserialize(
            new StringLiteral($invalidJson)
        );
    }

    /**
     * @return array
     */
    public function requiredPropertyDataProvider()
    {
        return [
            [
                '{"to": "2015-07-29"}',
                'from',
            ],
            [
                '{"from": "2014-08-06"}',
                'to',
            ],
        ];
    }

    /**
     * @test
     * @dataProvider invalidPropertyDataProvider
     *
     * @param string $invalidJson
     * @param string $invalidProperty
     */
    public function it_throws_an_exception_when_a_required_property_is_invalid(
        $invalidJson,
        $invalidProperty
    ) {
        $this->setExpectedException(
            IncorrectParameterValueException::class,
            sprintf('Incorrect value for parameter "%s".', $invalidProperty)
        );
        $this->deserializer->deserialize(
            new StringLiteral($invalidJson)
        );
    }

    /**
     * @return array
     */
    public function invalidPropertyDataProvider()
    {
        return [
            [
                '{"from": "01/02/2013", "to": "2015-07-29"}',
                'from',
            ],
            [
                '{"from": "2014-08-06", "to": "02/29/2015"}',
                'to',
            ],
            [
                '{"from": "2015-01-02", "to": "2014-02-03"}',
                'to',
            ],
        ];
    }
}
