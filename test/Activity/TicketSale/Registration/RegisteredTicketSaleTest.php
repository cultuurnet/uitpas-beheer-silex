<?php

namespace CultuurNet\UiTPASBeheer\Activity\TicketSale\Registration;

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

class RegisteredTicketSaleTest extends \PHPUnit_Framework_TestCase
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
     * @var RegisteredTicketSale
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

        $this->ticketSale = new RegisteredTicketSale(
            $this->id,
            $this->price,
            $this->creationDate
        );
    }

    /**
     * @test
     */
    public function it_returns_the_ticket_sale_data()
    {
        $this->assertEquals($this->id, $this->ticketSale->getId());
        $this->assertEquals($this->price, $this->ticketSale->getPrice());
        $this->assertEquals($this->creationDate, $this->ticketSale->getCreationDate());
    }

    /**
     * @test
     */
    public function it_encodes_to_json()
    {
        $json = json_encode($this->ticketSale);
        $this->assertJsonEquals($json, 'Activity/data/ticket-sale/registered-ticket-sale.json');
    }

    /**
     * @test
     */
    public function it_can_be_instantiated_from_a_culturefeed_ticket_sale()
    {
        $cfTicketSale = new \CultureFeed_Uitpas_Event_TicketSale();
        $cfTicketSale->id = 30818;
        $cfTicketSale->price = 2;
        $cfTicketSale->creationDate = 1440079102;

        $this->assertEquals(
            $this->ticketSale,
            RegisteredTicketSale::fromCultureFeedTicketSale($cfTicketSale)
        );
    }
}
