<?php

namespace CultuurNet\UiTPASBeheer\PassHolder\Properties;

use CultuurNet\Deserializer\JSONDeserializer;
use CultuurNet\UiTPASBeheer\Exception\MissingPropertyException;
use ValueObjects\StringLiteral\StringLiteral;

class KansenstatuutJsonDeserializerTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var KansenstatuutJsonDeserializer
     */
    protected $deserializer;

    public function setUp()
    {
        $this->deserializer = new KansenstatuutJsonDeserializer();
    }

    /**
     * @test
     */
    public function it_should_not_create_a_kansenstatuut_when_an_end_date_is_missing()
    {
        $kansenstatuutData = new StringLiteral('{"beep": "boob"}');

        $this->setExpectedException(MissingPropertyException::class, 'Missing property "endDate".');

        $this->deserializer->deserialize($kansenstatuutData);
    }

    /**
     * @test
     */
    public function it_should_include_optional_remarks()
    {
        $kansenstatuutData = new StringLiteral('{"endDate": "2345-09-13T00:00:00+01:00", "remarks": "I am remarkable"}');

        $kansenstatuut = $this->deserializer->deserialize($kansenstatuutData);

        $expectedRemarks = new Remarks("I am remarkable");

        $this->assertEquals($expectedRemarks, $kansenstatuut->getRemarks());
    }
}
