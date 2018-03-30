<?php

namespace CultuurNet\UiTPASBeheer\Activity;

use ValueObjects\DateTime\DateTime;
use ValueObjects\Number\Integer;
use ValueObjects\StringLiteral\StringLiteral;

class PagedResultSetTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_refuses_results_of_an_incorrect_class()
    {
        $activities = [];

        $activities[] = new Activity(
            new StringLiteral('activity-1'),
            new StringLiteral('Activity title 1'),
            new StringLiteral('Activity description 1'),
            new CheckinConstraint(
                false,
                DateTime::fromNativeDateTime(new \DateTime()),
                DateTime::fromNativeDateTime(new \DateTime())
            ),
            new Integer(1)
        );

        $activities[] = new \stdClass();

        $this->setExpectedException(\InvalidArgumentException::class);

        new PagedResultSet(new Integer(2), $activities);
    }
}
