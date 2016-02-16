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
        $cfCounterA->id = 'unique-counter-id-A';
        $cfCounterA->name = 'School A';

        $cfCounterB = new \CultureFeed_Uitpas_Counter();
        $cfCounterB->id = 'unique-counter-id-B';
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

    /**
     * @test
     */
    public function serializes_all_schools_to_json()
    {
        $schoolA = new School(
            new StringLiteral('unique-id-A'),
            new StringLiteral('School A')
        );

        $schoolB = new School(
            new StringLiteral('unique-id-B'),
            new StringLiteral('School B')
        );

        $schools = SchoolCollection::fromArray([$schoolA, $schoolB]);

        $this->assertJsonStringEqualsJsonFile(
            __DIR__ . '/data/schools.json',
            json_encode($schools)
        );
    }
}
