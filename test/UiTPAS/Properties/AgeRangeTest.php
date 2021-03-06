<?php

namespace CultuurNet\UiTPASBeheer\UiTPAS\Properties;

use ValueObjects\Person\Age;

class AgeRangeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @tests
     */
    public function it_requires_at_least_one_limiter_to_create_a_range()
    {
        $this->setExpectedException(InvalidAgeRangeException::class);

        new AgeRange(null, null);
    }

    /**
     * @test
     */
    public function it_should_create_a_range_when_only_specifying_a_lower_limit()
    {
        $range = new AgeRange(Age::fromNative(5), null);

        $this->assertEquals(new Age(5), $range->getFrom());
        $this->assertNull($range->getTo());
    }

    /**
     * @test
     */
    public function it_should_create_a_range_when_only_an_upper_limit_is_specified()
    {
        $range = new AgeRange(null, Age::fromNative(7));

        $this->assertEquals(new Age(7), $range->getTo());
        $this->assertNull($range->getFrom());
    }

    /**
     * @test
     */
    public function it_should_prevent_a_range_from_ending_before_it_starts()
    {
        $this->setExpectedException(InvalidAgeRangeException::class);

        new AgeRange(new Age(10), new Age(5));
    }

    /**
     * @test
     */
    public function it_should_serialize_an_age_range_in_a_json_friendly_format()
    {
        $expectedJsonData = [
            "from" => 5,
            "to" => 10,
        ];

        $ageRange = new AgeRange(new Age(5), new Age(10));

        $this->assertEquals($expectedJsonData, $ageRange->jsonSerialize());
    }

    /**
     * @tests
     */
    public function it_requires_at_least_one_age_limiter_to_create_a_range()
    {
        $this->setExpectedException(\InvalidArgumentException::class);

        $cultureFeedUitpasAgeRange = new \CultureFeed_Uitpas_Passholder_AgeRange();
        AgeRange::fromCultureFeedUitpasAgeRange($cultureFeedUitpasAgeRange);

    }
}
