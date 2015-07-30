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
    public function it_omits_optional_properties_from_json()
    {
        $name = new Name($this->first, $this->last);
        $json = json_encode($name);
        $this->assertJsonEquals($json, 'PassHolder/data/properties/name-minimum.json');
    }
}
