<?php
/**
 * @file
 */

namespace CultuurNet\UiTPASBeheer\School;

use PHPUnit_Framework_TestCase;
use ValueObjects\StringLiteral\StringLiteral;

class SchoolCollectionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function can_be_created_from_an_array_of_culturefeed_counters()
    {
        $cfCounterA = new \CultureFeed_Uitpas_Counter();
        $cfCounterA->consumerKey = 'unique-counter-id-A';
        $cfCounterA->name = 'School A';

        $cfCounterB = new \CultureFeed_Uitpas_Counter();
        $cfCounterB->consumerKey = 'unique-counter-id-B';
        $cfCounterB->name = 'School B';

        $cfCounters = [
            $cfCounterA,
            $cfCounterB,
        ];

        $expectedSchools = SchoolCollection::fromArray(
            [
                new School(
                    new StringLiteral('unique-counter-id-A'),
                    new StringLiteral('School A')
                ),
                new School(
                    new StringLiteral('unique-counter-id-B'),
                    new StringLiteral('School B')
                ),
            ]
        );

        $schoolsCreatedFromCounters = SchoolCollection::fromCultureFeedCounters(
            $cfCounters
        );

        $this->assertEquals(
            $expectedSchools,
            $schoolsCreatedFromCounters
        );
    }
}
