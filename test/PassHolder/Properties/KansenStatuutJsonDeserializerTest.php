<?php

namespace CultuurNet\UiTPASBeheer\PassHolder\Properties;

use CultuurNet\Deserializer\JSONDeserializer;
use CultuurNet\UiTPASBeheer\Exception\MissingPropertyException;
use ValueObjects\StringLiteral\StringLiteral;

class KansenStatuutJsonDeserializerTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var KansenStatuutJsonDeserializer
     */
    protected $deserializer;

    public function setUp()
    {
        $this->deserializer = new KansenStatuutJsonDeserializer();
    }

    /**
     * @test
     */
    public function it_should_not_create_a_kansenstatuut_when_an_end_date_is_missing()
    {
        $kansenStatuutData = new StringLiteral('{"beep": "boob"}');

        $this->setExpectedException(MissingPropertyException::class, 'Missing property "endDate".');

        $this->deserializer->deserialize($kansenStatuutData);
    }

    /**
     * @test
     */
    public function it_should_include_optional_remarks()
    {
        $kansenStatuutData = new StringLiteral('{"endDate": "2345-09-13", "remarks": "I am remarkable"}');

        $kansenStatuut = $this->deserializer->deserialize($kansenStatuutData);

        $expectedRemarks = new Remarks("I am remarkable");

        $this->assertEquals($expectedRemarks, $kansenStatuut->getRemarks());
    }

    /**
     * @test
     */
    public function it_should_include_empty_but_provided_remarks()
    {
        $kansenStatuutData = new StringLiteral('{"endDate": "2345-09-13", "remarks": ""}');

        $kansenStatuut = $this->deserializer->deserialize($kansenStatuutData);

        $expectedRemarks = new Remarks("");

        $this->assertEquals($expectedRemarks, $kansenStatuut->getRemarks());
    }

    /**
     * @test
     */
    public function it_does_not_accept_time()
    {
        $kansenStatuutData = new StringLiteral('{"endDate": "2345-09-13T00:00:00+01:00", "remarks": "I am remarkable"}');

        $this->setExpectedException(
            KansenStatuutEndDateInvalidException::class,
            'Invalid kansenstatuut end date "2345-09-13T00:00:00+01:00"'
        );

        $this->deserializer->deserialize($kansenStatuutData);
    }
}
