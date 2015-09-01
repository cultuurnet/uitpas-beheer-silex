<?php

namespace CultuurNet\UiTPASBeheer\UiTPAS;

use CultuurNet\UiTPASBeheer\CardSystem\Properties\CardSystemId;
use CultuurNet\UiTPASBeheer\JsonAssertionTrait;
use ValueObjects\StringLiteral\StringLiteral;

class UiTPASTest extends \PHPUnit_Framework_TestCase
{
    use JsonAssertionTrait;

    /**
     * @var string
     */
    protected $number = '0930000420206';

    /**
     * @var string
     */
    protected $city = 'Leuven';

    /**
     * @var \CultureFeed_Uitpas_Passholder_Card
     */
    protected $passholderCardMinimal;

    /**
     * @var \CultureFeed_Uitpas_Passholder_Card
     */
    protected $passholderCardFull;

    public function setUp()
    {
        $this->passholderCardMinimal = new \CultureFeed_Uitpas_Passholder_Card();
        $this->passholderCardMinimal->status = UiTPASStatus::ACTIVE;
        $this->passholderCardMinimal->type = UiTPASType::CARD();
        $this->passholderCardMinimal->uitpasNumber = $this->number;
        $this->passholderCardMinimal->cardSystemId = 666;

        $this->passholderCardFull = clone $this->passholderCardMinimal;
        $this->passholderCardFull->city = $this->city;
    }

    /**
     * @test
     */
    public function it_can_extract_properties_from_a_culturefeed_passholder_card()
    {
        $expected = (new UiTPAS(
            new UiTPASNumber($this->number),
            UiTPASStatus::ACTIVE(),
            UiTPASType::CARD(),
            new CardSystemId('666')
        ))->withCity(new StringLiteral($this->city));

        $actual = UiTPAS::fromCultureFeedPassHolderCard($this->passholderCardFull);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function it_can_manage_missing_properties_while_extracting_properties_from_a_culturefeed_passholder_card()
    {
        $expected = new UiTPAS(
            new UiTPASNumber($this->number),
            UiTPASStatus::ACTIVE(),
            UiTPASType::CARD(),
            new CardSystemId('666')
        );

        $actual = UiTPAS::fromCultureFeedPassHolderCard($this->passholderCardMinimal);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function it_can_serialize_to_json()
    {
        $uitpas = UiTPAS::fromCultureFeedPassHolderCard(
            $this->passholderCardFull
        );

        $json = json_encode($uitpas);

        $this->assertJsonEquals($json, 'UiTPAS/data/uitpas-complete.json');
    }

    /**
     * @test
     */
    public function it_can_manage_missing_properties_while_serializing_to_json()
    {
        $uitpas = UiTPAS::fromCultureFeedPassHolderCard(
            $this->passholderCardMinimal
        );

        $json = json_encode($uitpas);

        $this->assertJsonEquals($json, 'UiTPAS/data/uitpas-minimal.json');
    }
}
