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
     * @var StringLiteral
     */
    protected $foreignCity;

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
    public function it_encodes_all_data_with_foreign_city_to_json()
    {
        $address = (new Address(
            $this->postalCode,
            new StringLiteral('Buitenland')
        ))->withStreet($this->street)
        ->withforeignCity(new StringLiteral('Parijs'));

        $json = json_encode($address);
        $this->assertJsonEquals($json, 'PassHolder/data/properties/address-complete-foreign.json');
    }

    /**
     * @test
     */
    public function it_omits_empty_properties_from_json()
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
        $cfPassHolder->postalCode = '1090';
        $cfPassHolder->city = 'Jette (Brussel)';
        $cfPassHolder->street = 'Rue Ferd 123 /0001';

        $address = Address::fromCultureFeedPassHolder($cfPassHolder);

        $this->assertEquals(
            $cfPassHolder->postalCode,
            $address
                ->getPostalCode()
                ->toNative()
        );

        $this->assertEquals(
            $cfPassHolder->city,
            $address
                ->getCity()
                ->toNative()
        );

        $this->assertEquals(
            $cfPassHolder->street,
            $address
                ->getStreet()
                ->toNative()
        );
    }

    /**
     * @test
     */
    public function it_can_extract_properties_including_foreign_city_from_a_culturefeed_passholder()
    {
        $cfPassHolder = new \CultureFeed_Uitpas_Passholder();
        $cfPassHolder->postalCode = '1090';
        $cfPassHolder->city = 'Buitenland';
        $cfPassHolder->street = 'Rue Ferd 123 /0001';
        $cfPassHolder->foreignCity = 'Parijs';

        $address = Address::fromCultureFeedPassHolder($cfPassHolder);

        $this->assertEquals(
            $cfPassHolder->postalCode,
            $address
                ->getPostalCode()
                ->toNative()
        );

        $this->assertEquals(
            $cfPassHolder->city,
            $address
                ->getCity()
                ->toNative()
        );

        $this->assertEquals(
            $cfPassHolder->street,
            $address
                ->getStreet()
                ->toNative()
        );

        $this->assertEquals(
            $cfPassHolder->foreignCity,
            $address
                ->getForeignCity()
                ->toNative()
        );
    }
}
