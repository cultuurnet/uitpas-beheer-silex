<?php

namespace CultuurNet\UiTPASBeheer\PassHolder\Properties;

use CultuurNet\UiTPASBeheer\JsonAssertionTrait;
use ValueObjects\StringLiteral\StringLiteral;

class AddressTest extends \PHPUnit_Framework_TestCase
{
    use JsonAssertionTrait;

    /**
     * @var StringLiteral
     */
    protected $street;

    /**
     * @var StringLiteral
     */
    protected $postalCode;

    /**
     * @var StringLiteral
     */
    protected $city;

    /**
     * @var Address
     */
    protected $address;

    public function setUp()
    {
        $this->street = new StringLiteral('Rue Ferd 123 /0001');
        $this->postalCode = new StringLiteral('1090');
        $this->city = new StringLiteral('Jette (Brussel)');

        $this->address = (new Address(
            $this->postalCode,
            $this->city
        ))->withStreet($this->street);
    }

    /**
     * @test
     */
    public function it_encodes_all_data_to_json()
    {
        $json = json_encode($this->address);
        $this->assertJsonEquals($json, 'PassHolder/data/properties/address-complete.json');
    }

    /**
     * @test
     */
    public function it_omits_optional_properties_from_json()
    {
        $address = new Address(
            $this->postalCode,
            $this->city
        );
        $json = json_encode($address);
        $this->assertJsonEquals($json, 'PassHolder/data/properties/address-minimum.json');
    }

    /**
     * @test
     */
    public function it_can_extract_properties_from_a_culturefeed_passholder()
    {
        $cfPassHolder = new \CultureFeed_Uitpas_Passholder();
        $cfPassHolder->name = 'Zyrani';
        $cfPassHolder->firstName = 'Layla';
        $cfPassHolder->postalCode = '1090';
        $cfPassHolder->city = 'Jette (Brussel)';
        $cfPassHolder->street = 'Rue Ferd 123 /0001';

        $address = Address::fromCultureFeedPassHolder($cfPassHolder);
        $this->assertJsonEquals(json_encode($address), 'PassHolder/data/properties/address-complete.json');
    }

    /**
     * @test
     */
    public function it_can_serialize_to_json()
    {
        $address = new Address(
            new StringLiteral('1090'),
            new StringLiteral('Jette (Brussel)')
        );
        $address = $address->withStreet(new StringLiteral('Rue Ferd 123 /0001'));

        $this->assertJsonEquals(json_encode($address->jsonSerialize()), 'PassHolder/data/properties/address-complete.json');
    }
}
