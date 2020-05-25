<?php

namespace CultuurNet\UiTPASBeheer\PassHolder;

use CultuurNet\Deserializer\DeserializerInterface;
use CultuurNet\UiTPASBeheer\Exception\MissingPropertyException;
use CultuurNet\UiTPASBeheer\PassHolder\Properties\AddressJsonDeserializer;
use CultuurNet\UiTPASBeheer\PassHolder\Properties\BirthInformationJsonDeserializer;
use CultuurNet\UiTPASBeheer\PassHolder\Properties\ContactInformationJsonDeserializer;
use CultuurNet\UiTPASBeheer\PassHolder\Properties\NameJsonDeserializer;
use CultuurNet\UiTPASBeheer\PassHolder\Properties\OptInPreferencesJsonDeserializer;
use CultuurNet\UiTPASBeheer\School\SchoolJsonDeserializer;
use ValueObjects\StringLiteral\StringLiteral;

class PassHolderJsonDeserializerTest extends \PHPUnit_Framework_TestCase
{
    use PassHolderDataTrait;

    /**
     * @var PassHolderJsonDeserializer
     */
    protected $deserializer;

    public function setUp()
    {
        $this->deserializer = new PassHolderJsonDeserializer(
            new NameJsonDeserializer(),
            new AddressJsonDeserializer(),
            new BirthInformationJsonDeserializer(),
            new ContactInformationJsonDeserializer(),
            new SchoolJsonDeserializer(),
            new OptInPreferencesJsonDeserializer()
        );
    }

    private function getFullPassholderSample($decoded = true)
    {
        $json = file_get_contents(__DIR__ . '/data/passholder-update.json');

        if ($decoded) {
            return json_decode($json);
        }

        return $json;
    }

    /**
     * @test
     */
    public function it_can_deserialize_a_complete_passholder_json_object()
    {
        $expected = $this->getCompletePassHolderUpdate();

        $json = $this->getFullPassholderSample(false);

        $actual = $this->deserializer->deserialize(new StringLiteral($json));
        $this->assertEquals($expected->jsonSerialize(), $actual->jsonSerialize());
    }

    /**
     * @test
     */
    public function it_refuses_to_deserialize_when_name_is_missing()
    {
        $json = $this->getFullPassholderSample();
        unset($json->name);

        $this->setExpectedException(
            MissingPropertyException::class,
            'Missing property "name".'
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
    public function it_refuses_to_deserialize_when_address_is_missing()
    {
        $json = $this->getFullPassholderSample();
        unset($json->address);

        $this->setExpectedException(
            MissingPropertyException::class,
            'Missing property "address".'
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
    public function it_refuses_to_deserialize_when_birth_is_missing()
    {
        $json = $this->getFullPassholderSample();
        unset($json->birth);

        $this->setExpectedException(
            MissingPropertyException::class,
            'Missing property "birth".'
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
    public function it_refuses_to_deserialize_when_first_name_is_missing()
    {
        $json = $this->getFullPassholderSample();
        unset($json->name->first);

        $this->setExpectedException(
            MissingPropertyException::class,
            'Missing property "name->first".'
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
    public function it_refuses_to_deserialize_when_last_name_is_missing()
    {
        $json = $this->getFullPassholderSample();
        unset($json->name->last);

        $this->setExpectedException(
            MissingPropertyException::class,
            'Missing property "name->last".'
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
    public function it_refuses_to_deserialize_when_address_postal_code_is_missing()
    {
        $json = $this->getFullPassholderSample();
        unset($json->address->postalCode);

        $this->setExpectedException(
            MissingPropertyException::class,
            'Missing property "address->postalCode".'
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
    public function it_refuses_to_deserialize_when_address_city_is_missing()
    {
        $json = $this->getFullPassholderSample();
        unset($json->address->city);

        $this->setExpectedException(
            MissingPropertyException::class,
            'Missing property "address->city".'
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
    public function it_refuses_to_deserialize_when_birth_date_is_missing()
    {
        $json = $this->getFullPassholderSample();
        unset($json->birth->date);

        $this->setExpectedException(
            MissingPropertyException::class,
            'Missing property "birth->date".'
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
    public function it_refuses_to_deserialize_when_the_contact_information_deserializer_refuses()
    {
        $contactInformationJsonDeserializer = $this->getMock(DeserializerInterface::class);

        $deserializer = new PassHolderJsonDeserializer(
            new NameJsonDeserializer(),
            new AddressJsonDeserializer(),
            new BirthInformationJsonDeserializer(),
            $contactInformationJsonDeserializer,
            new SchoolJsonDeserializer(),
            new OptInPreferencesJsonDeserializer()
        );

        $contactInformationJsonDeserializer->expects($this->once())
            ->method('deserialize')
            ->will(
                $this->throwException(
                    new MissingPropertyException('email')
                )
            );

        $json = $this->getFullPassholderSample(false);

        $this->setExpectedException(
            MissingPropertyException::class,
            'Missing property "contact->email".'
        );

        $deserializer->deserialize(new StringLiteral($json));
    }

    /**
     * @test
     */
    public function it_refuses_to_deserialize_when_school_id_is_missing()
    {
        $json = $this->getFullPassholderSample();
        unset($json->school->id);

        $this->setExpectedException(
            MissingPropertyException::class,
            'Missing property "school->id".'
        );

        $this->deserializer->deserialize(
            new StringLiteral(
                json_encode($json)
            )
        );
    }
}
