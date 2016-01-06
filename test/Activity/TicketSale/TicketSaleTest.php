<?php

namespace CultuurNet\UiTPASBeheer\Activity\TicketSale;

use CultuurNet\UiTPASBeheer\Coupon\Coupon;
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
use ValueObjects\Number\Real;
use ValueObjects\StringLiteral\StringLiteral;

class TicketSaleTest extends \PHPUnit_Framework_TestCase
{
    use JsonAssertionTrait;

    /**
     * @var StringLiteral
     */
    protected $id;

    /**
     * @var Real
     */
    protected $price;

    /**
     * @var DateTime
     */
    protected $creationDate;

    /**
     * @var StringLiteral
     */
    protected $eventTitle;

    /**
     * @var Coupon
     */
    protected $coupon;

    /**
     * @var TicketSale
     */
    protected $minimalTicketSale;

    /**
     * @var TicketSale
     */
    protected $ticketSale;

    public function setUp()
    {
        date_default_timezone_set('UTC');

        $this->id = new StringLiteral('30818');
        $this->price = new Real(2.0);
        $this->creationDate = new DateTime(
            new Date(
                new Year(2015),
                Month::getByName('AUGUST'),
                new MonthDay('20')
            ),
            new Time(
                new Hour(13),
                new Minute(58),
                new Second(22)
            )
        );
        $this->eventTitle = new StringLiteral('Foo Bar');
        $this->coupon = new Coupon(
            new StringLiteral('5'),
            new StringLiteral('Demo coupon')
        );

        $this->minimalTicketSale = new TicketSale(
            $this->id,
            $this->price,
            $this->creationDate,
            $this->eventTitle
        );

        $this->ticketSale = $this->minimalTicketSale->withCoupon($this->coupon);
    }

    /**
     * @test
     */
    public function it_returns_the_ticket_sale_data()
    {
        $this->assertEquals($this->id, $this->ticketSale->getId());
        $this->assertEquals($this->price, $this->ticketSale->getPrice());
        $this->assertEquals($this->creationDate, $this->ticketSale->getCreationDate());
        $this->assertEquals($this->eventTitle, $this->ticketSale->getEventTitle());
        $this->assertEquals($this->coupon, $this->ticketSale->getCoupon());
    }

    /**
     * @test
     */
    public function it_encodes_to_json()
    {
        $minimalJson = json_encode($this->minimalTicketSale);
        $this->assertJsonEquals($minimalJson, 'Activity/data/ticket-sale/ticket-sale-minimal.json');

        $json = json_encode($this->ticketSale);
        $this->assertJsonEquals($json, 'Activity/data/ticket-sale/ticket-sale-complete.json');
    }

    /**
     * @test
     */
    public function it_can_be_instantiated_from_a_culturefeed_ticket_sale()
    {
        $cfTicketSale = new \CultureFeed_Uitpas_Event_TicketSale();
        $cfTicketSale->id = 30818;
        $cfTicketSale->tariff = 2;
        $cfTicketSale->creationDate = 1440079102;
        $cfTicketSale->nodeTitle = 'Foo Bar';

        $cfTicketSale->ticketSaleCoupon = new \CultureFeed_Uitpas_Event_TicketSale_Coupon();
        $cfTicketSale->ticketSaleCoupon->id = '5';
        $cfTicketSale->ticketSaleCoupon->name = 'Demo coupon';

        $this->assertEquals(
            $this->ticketSale,
            TicketSale::fromCultureFeedTicketSale($cfTicketSale)
        );
    }
}
