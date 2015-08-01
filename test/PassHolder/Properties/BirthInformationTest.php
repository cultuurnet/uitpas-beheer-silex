<?php

namespace CultuurNet\UiTPASBeheer\PassHolder\Properties;

use CultuurNet\UiTPASBeheer\JsonAssertionTrait;
use ValueObjects\DateTime\Date;
use ValueObjects\StringLiteral\StringLiteral;

class BirthInformationTest extends \PHPUnit_Framework_TestCase
{
    use JsonAssertionTrait;

    /**
     * @var Date
     */
    protected $date;

    /**
     * @var StringLiteral
     */
    protected $place;

    /**
     * @var BirthInformation
     */
    protected $birthInformation;

    public function setUp()
    {
        $dateTime = new \DateTime('1976-08-13');
        $this->date = Date::fromNativeDateTime($dateTime);

        $this->place  = new StringLiteral('Casablanca');

        $this->birthInformation = new BirthInformation($this->date);
        $this->birthInformation = $this->birthInformation->withPlace($this->place);
    }

    /**
     * @test
     */
    public function it_encodes_all_data_to_json()
    {
        $json = json_encode($this->birthInformation);
        $this->assertJsonEquals($json, 'PassHolder/data/properties/birth-information-complete.json');
    }

    /**
     * @test
     */
    public function it_omits_empty_data_from_json()
    {
        $birthInformation = new BirthInformation($this->date);
        $json = json_encode($birthInformation);
        $this->assertJsonEquals($json, 'PassHolder/data/properties/birth-information-minimum.json');
    }

    /**
     * @test
     */
    public function it_can_extract_properties_from_a_culturefeed_passholder()
    {
        $cfPassHolder = new \CultureFeed_Uitpas_Passholder();
        $cfPassHolder->dateOfBirth = 208742400;
        $cfPassHolder->placeOfBirth = 'Casablanca';

        $birthInfo = BirthInformation::fromCultureFeedPassHolder($cfPassHolder);

        $date = Date::fromNativeDateTime(
            \DateTime::createFromFormat('U', $cfPassHolder->dateOfBirth)
        );
        $this->assertTrue($birthInfo->getDate()->sameValueAs($date));

        $this->assertEquals(
            $cfPassHolder->placeOfBirth,
            $birthInfo->getPlace()
        );
    }
}
