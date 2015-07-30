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

        $this->address = new Address(
            $this->postalCode,
            $this->city
        );
    }

    /**
     * @test
     */
    public function it_encodes_all_data_to_json()
    {
        $this->address = $this->address->withStreet($this->street);
        $json = json_encode($this->address);

        $this->assertJsonEquals($json, 'PassHolder/data/properties/address-complete.json');
    }

    /**
     * @test
     */
    public function it_omits_optional_properties_from_json()
    {
        $json = json_encode($this->address);
        $this->assertJsonEquals($json, 'PassHolder/data/properties/address-minimum.json');
    }
}
