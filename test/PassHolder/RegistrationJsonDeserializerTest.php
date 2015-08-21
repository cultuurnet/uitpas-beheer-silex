<?php

namespace CultuurNet\UiTPASBeheer\PassHolder;

use CultuurNet\Deserializer\DeserializerInterface;
use CultuurNet\UiTPASBeheer\Exception\MissingPropertyException;
use CultuurNet\UiTPASBeheer\PassHolder\Properties\Kansenstatuut;
use CultuurNet\UiTPASBeheer\PassHolder\Properties\KansenstatuutJsonDeserializer;
use ValueObjects\DateTime\Date;
use ValueObjects\StringLiteral\StringLiteral;

class RegistrationJsonDeserializerTest extends \PHPUnit_Framework_TestCase
{
    use PassHolderDataTrait;

    /**
     * @var PassHolderJsonDeserializer
     */
    protected $deserializer;

    /**
     * @var DeserializerInterface
     */
    protected $passholderDeserializer;

    /**
     * @var DeserializerInterface
     */
    protected $kansenstatuutDeserializer;

    public function setUp()
    {
        $this->passholderDeserializer = $this->getMock(DeserializerInterface::class);
        $this->kansenstatuutDeserializer = $this->getMock(DeserializerInterface::class);

        $this->deserializer = new RegistrationJsonDeserializer(
            $this->passholderDeserializer,
            $this->kansenstatuutDeserializer
        );
    }

    /**
     * @test
     */
    public function it_should_deserialize_registration_json_data_with_all_additional_info()
    {
        $this->passholderDeserializer
            ->expects($this->once())
            ->method('deserialize')
            ->willReturn($this->getCompletePassHolder());

        $this->kansenstatuutDeserializer
            ->expects($this->once())
            ->method('deserialize')
            ->willReturn(new Kansenstatuut(Date::now()));

        $registration = $this->deserializer->deserialize(
            new StringLiteral($this->getPassholderRegistrationSample(false))
        );

        $this->assertEquals('i-am-voucher', $registration->getVoucherNumber());
        $this->assertInstanceOf(PassHolder::class, $registration->getPassholder());
        $this->assertInstanceOf(Kansenstatuut::class, $registration->getKansenstatuut());
    }

    /**
     * @test
     */
    public function it_refuses_to_deserialize_when_a_passholder_is_missing()
    {
        $json = $this->getPassholderRegistrationSample();
        unset($json->passholder);

        $this->setExpectedException(
            MissingPropertyException::class,
            'Missing property "passholder".'
        );

        $this->deserializer->deserialize(
            new StringLiteral(
                json_encode($json)
            )
        );
    }

    /**
     * @test
     */
    public function it_refuses_to_deserialize_when_the_passholder_is_malformed()
    {
        $this->passholderDeserializer
            ->expects($this->once())
            ->method('deserialize')
            ->will(
                $this->throwException(
                    new MissingPropertyException('email')
                )
            );

        $this->setExpectedException(
            MissingPropertyException::class,
            'Missing property "passholder->email".'
        );

        $this->deserializer->deserialize(
            new StringLiteral($this->getPassholderRegistrationSample(false))
        );
    }

    /**
     * @test
     */
    public function it_refuses_to_deserialize_when_the_kansenstatuut_is_malformed()
    {
        $this->passholderDeserializer
            ->expects($this->once())
            ->method('deserialize')
            ->willReturn($this->getCompletePassHolder());

        $this->kansenstatuutDeserializer
            ->expects($this->once())
            ->method('deserialize')
            ->will(
                $this->throwException(
                    new MissingPropertyException('required-property')
                )
            );

        $this->setExpectedException(
            MissingPropertyException::class,
            'Missing property "kansenstatuut->required-property".'
        );

        $this->deserializer->deserialize(
            new StringLiteral($this->getPassholderRegistrationSample(false))
        );
    }

    private function getPassholderRegistrationSample($decoded = true)
    {
        $json = file_get_contents(__DIR__ . '/data/passholder-registration.json');

        if ($decoded) {
            return json_decode($json);
        }

        return $json;
    }
}
