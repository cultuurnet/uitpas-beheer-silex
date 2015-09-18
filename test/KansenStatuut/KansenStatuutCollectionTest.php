<?php

namespace CultuurNet\UiTPASBeheer\KansenStatuut;

use CultuurNet\UiTPASBeheer\CardSystem\CardSystem;
use CultuurNet\UiTPASBeheer\CardSystem\Properties\CardSystemId;
use CultuurNet\UiTPASBeheer\JsonAssertionTrait;
use ValueObjects\DateTime\Date;
use ValueObjects\DateTime\Month;
use ValueObjects\DateTime\MonthDay;
use ValueObjects\DateTime\Year;
use ValueObjects\StringLiteral\StringLiteral;

class KansenStatuutCollectionTest extends \PHPUnit_Framework_TestCase
{
    use JsonAssertionTrait;

    /**
     * @var KansenStatuut
     */
    protected $bxl;

    /**
     * @var KansenStatuut
     */
    protected $aalst;

    /**
     * @var KansenStatuutCollection
     */
    protected $collection;

    /**
     * @test
     */
    public function setUp()
    {
        $this->bxl = (new KansenStatuut(
            new Date(
                new Year('2015'),
                Month::getByName('DECEMBER'),
                new MonthDay('24')
            )
        ))->withStatus(
            KansenStatuutStatus::EXPIRED()
        )->withCardSystem(
            new CardSystem(
                new CardSystemId('666'),
                new StringLiteral('UiTPAS Regio BxL')
            )
        );

        $this->aalst = (new KansenStatuut(
            new Date(
                new Year('2015'),
                Month::getByName('DECEMBER'),
                new MonthDay('26')
            )
        ))->withStatus(
            KansenStatuutStatus::IN_GRACE_PERIOD()
        )->withCardSystem(
            new CardSystem(
                new CardSystemId('999'),
                new StringLiteral('UiTPAS Regio Aalst')
            )
        );

        $this->collection = (new KansenStatuutCollection())
            ->withKey('666', $this->bxl)
            ->withKey('999', $this->aalst);
    }

    /**
     * @test
     */
    public function it_can_be_encoded_to_json()
    {
        $json = json_encode($this->collection);
        $this->assertJsonEquals($json, 'KansenStatuut/data/kansen-statuut-collection.json');
    }

    /**
     * @test
     */
    public function it_can_instantiated_from_an_array_of_culturefeed_card_system_specifics()
    {
        $cfCardSystemSpecificArray = [];

        $cfBxl = new \CultureFeed_Uitpas_Passholder_CardSystemSpecific();
        $cfBxl->kansenStatuut = true;
        $cfBxl->kansenStatuutEndDate = $this->bxl->getEndDate()->toNativeDateTime()->getTimestamp();
        $cfBxl->kansenStatuutExpired = true;
        $cfBxl->kansenStatuutInGracePeriod = false;
        $cfBxl->cardSystem = new \CultureFeed_Uitpas_CardSystem(
            666,
            'UiTPAS Regio BxL'
        );
        $cfCardSystemSpecificArray[666] = $cfBxl;

        $cfAalst = new \CultureFeed_Uitpas_Passholder_CardSystemSpecific();
        $cfAalst->kansenStatuut = true;
        $cfAalst->kansenStatuutEndDate = $this->aalst->getEndDate()->toNativeDateTime()->getTimestamp();
        $cfAalst->kansenStatuutExpired = false;
        $cfAalst->kansenStatuutInGracePeriod = true;
        $cfAalst->cardSystem = new \CultureFeed_Uitpas_CardSystem(
            999,
            'UiTPAS Regio Aalst'
        );
        $cfCardSystemSpecificArray[999] = $cfAalst;

        $kansenStatuutCollection = KansenStatuutCollection::fromCultureFeedPassholderCardSystemSpecific(
            $cfCardSystemSpecificArray
        );

        $this->assertEquals($this->collection, $kansenStatuutCollection);
    }
}
