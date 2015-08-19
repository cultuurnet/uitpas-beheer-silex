<?php

namespace CultuurNet\UiTPASBeheer\Activity;

use CultuurNet\UiTPASBeheer\Activity\TicketSale\SalesInformation;
use CultuurNet\UiTPASBeheer\Activity\TicketSale\SalesInformationTestDataTrait;
use CultuurNet\UiTPASBeheer\JsonAssertionTrait;
use ValueObjects\DateTime\Date;
use ValueObjects\DateTime\DateTime;
use ValueObjects\DateTime\Hour;
use ValueObjects\DateTime\Minute;
use ValueObjects\DateTime\Month;
use ValueObjects\DateTime\MonthDay;
use ValueObjects\DateTime\Second;
use ValueObjects\DateTime\Time;
use ValueObjects\DateTime\Year;
use ValueObjects\Number\Integer;
use ValueObjects\StringLiteral\StringLiteral;

class ActivityTest extends \PHPUnit_Framework_TestCase
{
    use JsonAssertionTrait;
    use SalesInformationTestDataTrait;

    /**
     * @var Activity
     */
    protected $activity;

    /**
     * @var StringLiteral
     */
    protected $when;

    /**
     * @var StringLiteral
     */
    protected $description;

    /**
     * @var StringLiteral
     */
    protected $id;

    /**
     * @var StringLiteral
     */
    protected $title;

    /**
     * @var CheckinConstraint
     */
    protected $checkinConstraint;

    /**
     * @var Integer
     */
    protected $points;

    /**
     * @var SalesInformation
     */
    protected $salesInformation;

    public function setUp()
    {
        $this->id = new StringLiteral('10');
        $this->title = new StringLiteral('Some title');
        $this->description = new StringLiteral('Some description');
        $this->when = new StringLiteral('yesterday');
        $this->points = new Integer(1);

        $checkinStartDate = new DateTime(
            new Date(
                Year::fromNative(2015),
                Month::getByName('SEPTEMBER'),
                MonthDay::fromNative(1)
            ),
            new Time(
                Hour::fromNative(9),
                Minute::fromNative(0),
                Second::fromNative(0)
            )
        );

        $checkinEndDate = new DateTime(
            new Date(
                Year::fromNative(2016),
                Month::getByName('MARCH'),
                MonthDay::fromNative(1)
            ),
            new Time(
                Hour::fromNative(16),
                Minute::fromNative(0),
                Second::fromNative(0)
            )
        );

        $this->checkinConstraint = new CheckinConstraint(
            false,
            $checkinStartDate,
            $checkinEndDate
        );
        $this->checkinConstraint = $this->checkinConstraint->withReason(new StringLiteral('INVALID_DATE_TIME'));

        $this->activity = new Activity(
            $this->id,
            $this->title,
            $this->checkinConstraint,
            $this->points
        );

        $this->salesInformation = $this->getSampleInformationWithTariffs();

        $this->activity = $this->activity
            ->withWhen($this->when)
            ->withDescription($this->description)
            ->withSalesInformation($this->salesInformation);
    }

    /**
     * @test
     */
    public function it_can_return_the_properties()
    {
        $this->assertEquals($this->id, $this->activity->getId());
        $this->assertEquals($this->title, $this->activity->getTitle());
        $this->assertEquals($this->description, $this->activity->getDescription());
        $this->assertEquals($this->when, $this->activity->getWhen());
        $this->assertEquals($this->checkinConstraint, $this->activity->getCheckinConstraint());
        $this->assertEquals($this->points, $this->activity->getPoints());
        $this->assertEquals($this->salesInformation, $this->activity->getSalesInformation());
    }

    /**
     * @test
     */
    public function it_can_be_json_encoded()
    {
        $this->assertJsonEquals(
            json_encode($this->activity),
            'Activity/data/activity.json'
        );
    }

    /**
     * @test
     */
    public function it_can_be_instantiated_from_a_culturefeed_uitpas_event()
    {
        $cfEvent = new \CultureFeed_Uitpas_Event_CultureEvent();

        $cfEvent->cdbid = $this->id->toNative();
        $cfEvent->title = $this->title->toNative();
        $cfEvent->checkinAllowed = $this->checkinConstraint->getAllowed();
        $cfEvent->checkinStartDate = $this->checkinConstraint->getStartDate()->toNativeDateTime()->format('U');
        $cfEvent->checkinEndDate = $this->checkinConstraint->getEndDate()->toNativeDateTime()->format('U');

        $cfFirstPriceClass = new \CultureFeed_Uitpas_Event_PriceClass();
        $cfFirstPriceClass->name = 'Rang 1';
        $cfFirstPriceClass->price = 30;
        $cfFirstPriceClass->tariff = 22;

        $cfSecondPriceClass = new \CultureFeed_Uitpas_Event_PriceClass();
        $cfSecondPriceClass->name = 'Rang 2';
        $cfSecondPriceClass->price = 15;
        $cfSecondPriceClass->tariff = 11;

        $cfThirdPriceClass = new \CultureFeed_Uitpas_Event_PriceClass();
        $cfThirdPriceClass->name = 'Rang 3+';
        $cfThirdPriceClass->price = 7.5;
        $cfThirdPriceClass->tariff = 5.5;

        $cfKansentarief = new \CultureFeed_Uitpas_Event_TicketSale_Opportunity();
        $cfKansentarief->type = \CultureFeed_Uitpas_Event_TicketSale_Opportunity::TYPE_DEFAULT;
        $cfKansentarief->priceClasses = array(
            $cfFirstPriceClass,
            $cfSecondPriceClass,
            $cfThirdPriceClass,
        );

        $cfTicketSaleCoupon = new \CultureFeed_Uitpas_Event_TicketSale_Coupon();
        $cfTicketSaleCoupon->name = 'Cultuurwaardebon';

        $cfCoupon = new \CultureFeed_Uitpas_Event_TicketSale_Opportunity();
        $cfCoupon->type = \CultureFeed_Uitpas_Event_TicketSale_Opportunity::TYPE_COUPON;
        $cfCoupon->priceClasses = array(
            $cfFirstPriceClass,
            $cfSecondPriceClass,
            $cfThirdPriceClass,
        );
        $cfCoupon->ticketSaleCoupon = $cfTicketSaleCoupon;

        $cfEvent->ticketSales = array(
            $cfKansentarief,
            $cfCoupon,
        );
    }
}
