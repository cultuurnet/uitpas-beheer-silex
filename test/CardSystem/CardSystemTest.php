<?php

namespace CultuurNet\UiTPASBeheer\CardSystem;

use CultuurNet\UiTPASBeheer\CardSystem\Properties\CardSystemId;
use CultuurNet\UiTPASBeheer\JsonAssertionTrait;
use ValueObjects\StringLiteral\StringLiteral;

class CardSystemTest extends \PHPUnit_Framework_TestCase
{
    use JsonAssertionTrait;

    /**
     * @var CardSystemId
     */
    protected $id;

    /**
     * @var StringLiteral
     */
    protected $name;

    /**
     * @var CardSystem
     */
    protected $cardSystem;

    public function setUp()
    {
        $this->id = new CardSystemId('5');
        $this->name = new StringLiteral('UiTPAS Regio Danny');

        $this->cardSystem = new CardSystem(
            $this->id,
            $this->name
        );
    }

    /**
     * @test
     */
    public function it_returns_all_properties()
    {
        $this->assertEquals($this->id, $this->cardSystem->getId());
        $this->assertEquals($this->name, $this->cardSystem->getName());
    }

    /**
     * @test
     */
    public function it_encodes_to_json()
    {
        $json = json_encode($this->cardSystem);
        $this->assertJsonEquals($json, 'CardSystem/data/card-system.json');
    }

    /**
     * @test
     */
    public function it_can_be_initialized_from_a_culturefeed_uitpas_card_system()
    {
        $cfCardSystem = new \CultureFeed_Uitpas_CardSystem();
        $cfCardSystem->id = 5;
        $cfCardSystem->name = 'UiTPAS Regio Danny';

        $cardSystem = CardSystem::fromCultureFeedCardSystem($cfCardSystem);

        $this->assertEquals($this->cardSystem, $cardSystem);
    }
}
