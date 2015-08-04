<?php

namespace CultuurNet\UiTPASBeheer\PassHolder\Properties;

use CultuurNet\UiTPASBeheer\JsonAssertionTrait;
use ValueObjects\StringLiteral\StringLiteral;

class NameTest extends \PHPUnit_Framework_TestCase
{
    use JsonAssertionTrait;

    /**
     * @var StringLiteral
     */
    protected $first;

    /**
     * @var StringLiteral
     */
    protected $middle;

    /**
     * @var StringLiteral
     */
    protected $last;

    /**
     * @var Name
     */
    protected $name;

    public function setUp()
    {
        $this->first = new StringLiteral('Layla');
        $this->middle = new StringLiteral('Zooni');
        $this->last = new StringLiteral('Zyrani');

        $this->name = (new Name($this->first, $this->last))
            ->withMiddleName($this->middle);
    }

    /**
     * @test
     */
    public function it_encodes_all_data_to_json()
    {
        $json = json_encode($this->name);
        $this->assertJsonEquals($json, 'PassHolder/data/properties/name-complete.json');
    }

    /**
     * @test
     */
    public function it_omits_empty_properties_from_json()
    {
        $name = new Name($this->first, $this->last);
        $json = json_encode($name);
        $this->assertJsonEquals($json, 'PassHolder/data/properties/name-minimum.json');
    }

    /**
     * @test
     */
    public function it_can_extract_properties_from_a_culturefeed_passholder()
    {
        $cfPassHolder = new \CultureFeed_Uitpas_Passholder();
        $cfPassHolder->name = 'Zyrani';
        $cfPassHolder->firstName = 'Layla';
        $cfPassHolder->secondName = 'Zooni';

        $name = Name::fromCultureFeedPassHolder($cfPassHolder);

        $this->assertEquals(
            $cfPassHolder->name,
            $name
                ->getLastName()
                ->toNative()
        );

        $this->assertEquals(
            $cfPassHolder->firstName,
            $name
                ->getFirstName()
                ->toNative()
        );

        $this->assertEquals(
            $cfPassHolder->secondName,
            $name
                ->getMiddleName()
                ->toNative()
        );
    }

    /**
     * @test
     */
    public function it_sets_filler_values_for_missing_name_information()
    {
        $cfPassHolder = new \CultureFeed_Uitpas_Passholder();

        $name = Name::fromCultureFeedPassHolder($cfPassHolder);

        // By using toNative() we check that they're not null, as well as that
        // their values are empty strings.
        $this->assertEmpty($name->getLastName()->toNative());
        $this->assertEmpty($name->getFirstName()->toNative());
    }
}
