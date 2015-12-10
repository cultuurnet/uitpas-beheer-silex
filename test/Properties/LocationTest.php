<?php

namespace CultuurNet\UiTPASBeheer\Properties;

use CultuurNet\UiTPASBeheer\JsonAssertionTrait;
use ValueObjects\StringLiteral\StringLiteral;

class LocationTest extends \PHPUnit_Framework_TestCase
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
    protected $name;

    /**
     * @var Address
     */
    protected $address;

    /**
     * @var Location
     */
    protected $location;

    public function setUp()
    {
        $this->name = new StringLiteral('CC De Werf');
        $this->street = new StringLiteral('Rue Ferd 123 /0001');
        $this->postalCode = new StringLiteral('1090');
        $this->city = new StringLiteral('Jette (Brussel)');

        $this->address = (new Address(
            $this->postalCode,
            $this->city
        ))->withStreet($this->street);

        $this->location = new Location($this->name, $this->address);
    }

    /**
     * @test
     */
    public function it_encodes_all_data_to_json()
    {
        $json = json_encode($this->location);
        $this->assertJsonEquals($json, 'Properties/data/location-complete.json');
    }

    /**
     * @test
     */
    public function it_can_extract_properties_from_a_culturefeed_passholder()
    {
        $this->assertEquals(
            '1090',
            $this->location
                ->getAddress()
                ->getPostalCode()
                ->toNative()
        );

        $this->assertEquals(
            'Jette (Brussel)',
            $this->location
                ->getAddress()
                ->getCity()
                ->toNative()
        );

        $this->assertEquals(
            'Rue Ferd 123 /0001',
            $this->location
                ->getAddress()
                ->getStreet()
                ->toNative()
        );

        $this->assertEquals(
            'CC De Werf',
            $this->location
                ->getName()
                ->toNative()
        );
    }

    /**
     * @test
     */
    public function it_can_check_if_a_location_is_the_same_as_another()
    {
        $this->assertTrue($this->location->sameValueAs($this->location));
    }
}
