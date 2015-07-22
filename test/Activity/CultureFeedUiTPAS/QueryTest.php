<?php
/**
 * @file
 */

namespace CultuurNet\UiTPASBeheer\Activity\CultureFeedUiTPAS;

use CultuurNet\Clock\FrozenClock;
use CultuurNet\UiTPASBeheer\Activity\DateType;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASNumber;
use ValueObjects\Number\Integer;
use ValueObjects\StringLiteral\StringLiteral;

class QueryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Query
     */
    protected $query;

    /**
     * @var FrozenClock
     */
    protected $clock;

    /**
     * @var \CultureFeed_Uitpas_Event_Query_SearchEventsOptions
     */
    protected $searchEventsOptions;

    public function setUp()
    {
        $this->clock = new FrozenClock(
            new \DateTime('2000-01-01', new \DateTimeZone('Europe/Brussels'))
        );

        $this->query = (new Query($this->clock))
            ->withPagination(new Integer(2), new Integer(10))
            ->withUiTPASNumber(new UiTPASNumber('0930000208908'))
            ->withQuery(new StringLiteral('foo'))
            ->withSort('permanent desc,availableto asc');

        $this->searchEventsOptions = new \CultureFeed_Uitpas_Event_Query_SearchEventsOptions(
        );
        $this->searchEventsOptions->max = 10;
        $this->searchEventsOptions->start = 10;
        $this->searchEventsOptions->uitpasNumber = '0930000208908';
        $this->searchEventsOptions->sort = 'permanent desc,availableto asc';
        $this->searchEventsOptions->q = 'foo';
    }

    /**
     * @test
     */
    public function it_can_build_SearchEventsOptions()
    {
        $query = $this->query->withDateType(DateType::NEXT_12_MONTHS());

        $expectedSearchEventsOptions = clone $this->searchEventsOptions;
        $expectedSearchEventsOptions->datetype = 'next12months';

        $this->assertEquals(
            $expectedSearchEventsOptions,
            $query->build()
        );
    }

    /**
     * @test
     */
    public function it_can_build_SearchEventsOptions_for_the_past()
    {
        $query = $this->query->withDateType(DateType::PAST());

        $expectedSearchEventsOptions = clone $this->searchEventsOptions;
        $expectedSearchEventsOptions->startDate =
            \DateTime::createFromFormat(
                \DateTime::W3C,
                '1998-12-31T00:00:00+01:00'
            )
                ->getTimestamp();
        $expectedSearchEventsOptions->endDate =
            \DateTime::createFromFormat(
                \DateTime::W3C,
                '1999-12-31T23:59:59+01:00'
            )
                ->getTimestamp();

        $this->assertEquals(
            $expectedSearchEventsOptions,
            $query->build()
        );
    }
}
