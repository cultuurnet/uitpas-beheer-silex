<?php

namespace CultuurNet\UiTPASBeheer\UiTPAS;

use CultuurNet\UiTPASBeheer\CardSystem\CardSystem;
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

    /**
     * @var \CultureFeed_Uitpas_CardInfo
     */
    protected $cardInfo;

    public function setUp()
    {
        $status = UiTPASStatus::ACTIVE;
        $type = UiTPASType::CARD;

        $cardSystem = new \CultureFeed_Uitpas_CardSystem();
        $cardSystem->id = 999;
        $cardSystem->name = 'UiTPAS Regio Aalst';

        $this->passholderCardMinimal = new \CultureFeed_Uitpas_Passholder_Card();
        $this->passholderCardMinimal->status = $status;
        $this->passholderCardMinimal->type = $type;
        $this->passholderCardMinimal->uitpasNumber = $this->number;
        $this->passholderCardMinimal->cardSystem = $cardSystem;

        $this->passholderCardFull = clone $this->passholderCardMinimal;
        $this->passholderCardFull->city = $this->city;

        $this->cardInfo = new \CultureFeed_Uitpas_CardInfo();
        $this->cardInfo->status = $status;
        $this->cardInfo->type = $type;
        $this->cardInfo->uitpasNumber = $this->number;
        $this->cardInfo->cardSystem = $cardSystem;
    }

    /**
     * @test
     */
    public function it_returns_all_properties()
    {
        $number = new UiTPASNumber($this->number);
        $status = UiTPASStatus::ACTIVE();
        $type = UiTPASType::CARD();
        $cardSystem = new CardSystem(
            new CardSystemId('999'),
            new StringLiteral('UiTPAS Regio Aalst')
        );
        $city = new StringLiteral($this->city);

        $uitpas = (new UiTPAS(
            $number,
            $status,
            $type,
            $cardSystem
        ))->withCity($city);

        $this->assertEquals(
            $number,
            $uitpas->getNumber()
        );

        $this->assertEquals(
            $status,
            $uitpas->getStatus()
        );

        $this->assertEquals(
            $type,
            $uitpas->getType()
        );

        $this->assertEquals(
            $cardSystem,
            $uitpas->getCardSystem()
        );

        $this->assertEquals(
            $city,
            $uitpas->getCity()
        );
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
            new CardSystem(
                new CardSystemId('999'),
                new StringLiteral('UiTPAS Regio Aalst')
            )
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
            new CardSystem(
                new CardSystemId('999'),
                new StringLiteral('UiTPAS Regio Aalst')
            )
        );

        $actual = UiTPAS::fromCultureFeedPassHolderCard($this->passholderCardMinimal);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function it_can_extract_properties_from_a_culturefeed_card_info_object()
    {
        $expected = new UiTPAS(
            new UiTPASNumber($this->number),
            UiTPASStatus::ACTIVE(),
            UiTPASType::CARD(),
            new CardSystem(
                new CardSystemId('999'),
                new StringLiteral('UiTPAS Regio Aalst')
            )
        );

        $actual = UiTPAS::fromCultureFeedCardInfo($this->cardInfo);

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
