<?php

namespace CultuurNet\UiTPASBeheer\Advantage;

use CultuurNet\UiTPASBeheer\Properties\City;
use CultuurNet\UiTPASBeheer\Properties\CityCollection;
use ValueObjects\DateTime\Date;
use ValueObjects\DateTime\Month;
use ValueObjects\DateTime\MonthDay;
use ValueObjects\DateTime\Year;
use ValueObjects\Number\Integer;
use ValueObjects\StringLiteral\StringLiteral;

class WelcomeAdvantageTest extends \PHPUnit_Framework_TestCase
{
    use AdvantageAssertionTrait;

    const EXCHANGEABLE = true;
    const NOT_EXCHANGEABLE = false;

    /**
     * @test
     * @dataProvider welcomeAdvantageDataProvider
     *
     * @param StringLiteral $id
     * @param StringLiteral $title
     * @param $exchangeable
     */
    public function it_initiates_objects_with_a_fixed_type_and_points_and_stores_all_other_info(
        StringLiteral $id,
        StringLiteral $title,
        $exchangeable
    ) {
        $advantage = new WelcomeAdvantage($id, $title, $exchangeable);

        $this->assertTrue($advantage->getType()->sameValueAs(AdvantageType::WELCOME()));

        $this->assertAdvantageData(
            $advantage,
            $id,
            $title,
            new Integer(0),
            $exchangeable
        );
    }

    /**
     * @test
     * @dataProvider welcomeAdvantageDataProvider
     *
     * @param StringLiteral $id
     * @param StringLiteral $title
     * @param $exchangeable
     */
    public function it_can_create_an_instance_from_a_culturefeed_welcome_advantage_object(
        StringLiteral $id,
        StringLiteral $title,
        $exchangeable
    ) {
        $cfAdvantage = new \CultureFeed_Uitpas_Passholder_WelcomeAdvantage();
        $cfAdvantage->id = $id;
        $cfAdvantage->title = $title;
        $cfAdvantage->cashedIn = !$exchangeable;

        $cfAdvantage->description1 = 'First description';
        $cfAdvantage->description2 = 'Second description';
        $cfAdvantage->validForCities = [
            "Brussel",
            "Leuven",
        ];

        $validForCounters = array();
        for ($i = 0; $i <= 2; $i++) {
            $counter = new \CultureFeed_Uitpas_Passholder_Counter();
            $counter->id = $i + 1;
            $counter->name = "counter " . $counter->id;

            $validForCounters[$i] = $counter;
        }

        $endDate = strtotime('2015-12-04');

        $cfAdvantage->counters = $validForCounters;
        $cfAdvantage->cashingPeriodEnd = $endDate;


        $advantage = WelcomeAdvantage::fromCultureFeedWelcomeAdvantage($cfAdvantage);

        $validForCitiesCollection = (new CityCollection())
            ->with(new City("Brussel"))
            ->with(new City("Leuven"));

        $endDatValueObject = new Date(
            new Year(2015),
            Month::DECEMBER(),
            new MonthDay(4)
        );

        $expectedAdvantage = (new WelcomeAdvantage(
            $id,
            $title,
            $exchangeable
        ))
            ->withDescription1(new StringLiteral('First description'))
            ->withDescription2(new StringLiteral('Second description'))
            ->withValidForCities($validForCitiesCollection)
            ->withValidForCounters($validForCounters)
            ->withEndDate($endDatValueObject);

        $this->assertEquals(
            $expectedAdvantage,
            $advantage
        );
    }

    /**
     * @return array
     */
    public function welcomeAdvantageDataProvider()
    {
        return [
            [
                new StringLiteral('10'),
                new StringLiteral('Delicious coffee'),
                true,
            ],
            [
                new StringLiteral('11'),
                new StringLiteral('Expired offer'),
                false,
            ],
        ];
    }

    /**
     * @test
     * @dataProvider availableUnitsDataProvider
     *
     * @param int $max
     * @param int $taken
     * @param bool $expectedExchangeableStatus
     */
    public function it_takes_the_available_units_into_account_when_instantiating_from_a_culturefeed_welcome_advantage(
        $max,
        $taken,
        $expectedExchangeableStatus
    ) {
        $cfAdvantage = new \CultureFeed_Uitpas_Passholder_WelcomeAdvantage();
        $cfAdvantage->id = 1;
        $cfAdvantage->title = 'Test';
        $cfAdvantage->cashedIn = false;
        $cfAdvantage->maxAvailableUnits = $max;
        $cfAdvantage->unitsTaken = $taken;

        $advantage = WelcomeAdvantage::fromCultureFeedWelcomeAdvantage($cfAdvantage);

        $this->assertEquals($expectedExchangeableStatus, $advantage->isExchangeable());
    }

    /**
     * @return array
     */
    public function availableUnitsDataProvider()
    {
        return [
            [
                10,
                5,
                self::EXCHANGEABLE,
            ],
            [
                10,
                10,
                self::NOT_EXCHANGEABLE,
            ],
            [
                null,
                100,
                self::EXCHANGEABLE,
            ],
        ];
    }
}
