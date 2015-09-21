<?php

namespace CultuurNet\UiTPASBeheer\KansenStatuut;

use CultuurNet\UiTPASBeheer\CardSystem\CardSystem;
use CultuurNet\UiTPASBeheer\CardSystem\Properties\CardSystemId;
use CultuurNet\UiTPASBeheer\JsonAssertionTrait;
use CultuurNet\UiTPASBeheer\PassHolder\Properties\Remarks;
use ValueObjects\DateTime\Date;
use ValueObjects\DateTime\Month;
use ValueObjects\DateTime\MonthDay;
use ValueObjects\DateTime\Year;
use ValueObjects\StringLiteral\StringLiteral;

class KansenstatuutTest extends \PHPUnit_Framework_TestCase
{
    use JsonAssertionTrait;

    /**
     * @var Date
     */
    protected $endDate;

    /**
     * @var Remarks
     */
    protected $remarks;

    /**
     * @var KansenStatuutStatus
     */
    protected $status;

    /**
     * @var CardSystem
     */
    protected $cardSystem;

    /**
     * @var KansenStatuut
     */
    protected $kansenStatuut;

    public function setUp()
    {
        $this->endDate = new Date(
            new Year('2015'),
            Month::getByName('DECEMBER'),
            new MonthDay('26')
        );

        $this->remarks = new Remarks('beep boop');

        $this->status = KansenStatuutStatus::IN_GRACE_PERIOD();

        $this->cardSystem = new CardSystem(
            new CardSystemId('999'),
            new StringLiteral('UiTPAS Regio Aalst')
        );

        $this->kansenStatuut = (new KansenStatuut($this->endDate))
            ->withRemarks($this->remarks)
            ->withStatus($this->status)
            ->withCardSystem($this->cardSystem);
    }

    /**
     * @test
     */
    public function it_returns_any_properties_that_were_set_previously()
    {
        $this->assertEquals($this->endDate, $this->kansenStatuut->getEndDate());
        $this->assertEquals($this->remarks, $this->kansenStatuut->getRemarks());
        $this->assertEquals($this->status, $this->kansenStatuut->getStatus());
        $this->assertEquals($this->cardSystem, $this->kansenStatuut->getCardSystem());
    }

    /**
     * @test
     */
    public function it_can_be_encoded_to_json()
    {
        $json = json_encode($this->kansenStatuut);
        $this->assertJsonEquals($json, 'KansenStatuut/data/kansen-statuut-complete.json');
    }

    /**
     * @test
     */
    public function it_can_be_initialized_from_a_culturefeed_card_system_specific_object()
    {
        $cardSystemSpecific = new \CultureFeed_Uitpas_Passholder_CardSystemSpecific();
        $cardSystemSpecific->kansenStatuut = true;
        $cardSystemSpecific->kansenStatuutEndDate = 1442331412;
        $cardSystemSpecific->cardSystem = new \CultureFeed_Uitpas_CardSystem();
        $cardSystemSpecific->cardSystem->id = 5;
        $cardSystemSpecific->cardSystem->name = 'Test';

        $cardSystemSpecificExpired = clone $cardSystemSpecific;
        $cardSystemSpecificExpired->kansenStatuutExpired = true;

        $cardSystemSpecificInGracePeriod = clone $cardSystemSpecific;
        $cardSystemSpecificInGracePeriod->kansenStatuutInGracePeriod = true;

        // The backend also sets the expired flag while actually the kansenstatuut
        // is still in its grace period. We need to verify that while defining
        // the value of our status property the grace period flag gets
        // precedence on the expired flag.
        $cardSystemSpecificInGracePeriodWithExpiredFlag = clone $cardSystemSpecificInGracePeriod;
        $cardSystemSpecificInGracePeriodWithExpiredFlag->kansenStatuutExpired = true;

        $endDate = Date::fromNativeDateTime(
            \DateTime::createFromFormat('U', 1442331412)
        );

        $cardSystem = new CardSystem(
            new CardSystemId('5'),
            new StringLiteral('Test')
        );

        $kansenStatuut = (new KansenStatuut($endDate))
            ->withCardSystem($cardSystem);

        $active = $kansenStatuut->withStatus(KansenStatuutStatus::ACTIVE());
        $expired = $kansenStatuut->withStatus(KansenStatuutStatus::EXPIRED());
        $inGracePeriod = $kansenStatuut->withStatus(KansenStatuutStatus::IN_GRACE_PERIOD());

        $this->assertEquals(
            $active,
            KansenStatuut::fromCultureFeedCardSystemSpecific($cardSystemSpecific)
        );
        $this->assertEquals(
            $expired,
            KansenStatuut::fromCultureFeedCardSystemSpecific($cardSystemSpecificExpired)
        );
        $this->assertEquals(
            $inGracePeriod,
            KansenStatuut::fromCultureFeedCardSystemSpecific($cardSystemSpecificInGracePeriod)
        );
        $this->assertEquals(
            $inGracePeriod,
            KansenStatuut::fromCultureFeedCardSystemSpecific($cardSystemSpecificInGracePeriodWithExpiredFlag)
        );
    }

    /**
     * @test
     */
    public function it_throws_an_exception_for_a_card_system_specific_object_without_kansenstatuut()
    {
        $cardSystemSpecific = new \CultureFeed_Uitpas_Passholder_CardSystemSpecific();
        $cardSystemSpecific->kansenStatuut = false;

        $this->setExpectedException(\InvalidArgumentException::class);
        KansenStatuut::fromCultureFeedCardSystemSpecific($cardSystemSpecific);
    }
}
