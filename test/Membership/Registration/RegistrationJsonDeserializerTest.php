<?php

namespace CultuurNet\UiTPASBeheer\Membership\Registration;

use CultuurNet\UiTPASBeheer\Exception\MissingPropertyException;
use CultuurNet\UiTPASBeheer\Membership\Association\Properties\AssociationId;
use ValueObjects\DateTime\Date;
use ValueObjects\DateTime\Month;
use ValueObjects\DateTime\MonthDay;
use ValueObjects\DateTime\Year;
use ValueObjects\StringLiteral\StringLiteral;

class RegistrationJsonDeserializerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AssociationId
     */
    protected $associationId;

    /**
     * @var Date
     */
    protected $endDate;

    /**
     * @var Registration
     */
    protected $minimal;

    /**
     * @var Registration
     */
    protected $complete;

    /**
     * @var RegistrationJsonDeserializer
     */
    protected $deserializer;

    public function setUp()
    {
        $this->deserializer = new RegistrationJsonDeserializer();

        $this->associationId = new AssociationId('12345');
        $this->endDate = new Date(
            new Year(2015),
            Month::getByName('APRIL'),
            new MonthDay(23)
        );

        $this->minimal = new Registration($this->associationId);
        $this->complete = $this->minimal->withEndDate($this->endDate);
    }

    /**
     * @test
     */
    public function it_can_deserialize_a_complete_registration_object()
    {
        $json = file_get_contents(__DIR__ . '/data/registration-complete.json');
        $actual = $this->deserializer->deserialize(
            new StringLiteral($json)
        );
        $this->assertEquals($this->complete, $actual);
    }

    /**
     * @test
     */
    public function it_omits_optional_properties_when_not_provided()
    {
        $json = file_get_contents(__DIR__ . '/data/registration-minimal.json');
        $actual = $this->deserializer->deserialize(
            new StringLiteral($json)
        );
        $this->assertEquals($this->minimal, $actual);
    }

    /**
     * @test
     */
    public function it_throws_an_exception_when_association_id_is_missing()
    {
        $json = '{"endDate": "2015-04-23"}';

        $this->setExpectedException(MissingPropertyException::class);

        $this->deserializer->deserialize(
            new StringLiteral($json)
        );
    }
}
