<?php

namespace CultuurNet\UiTPASBeheer\Activity;

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
            new StringLiteral('Activity 1'),
            new StringLiteral('Description of activity 1'),
            new StringLiteral('Each Wednesday')
        );

        $activities[] = new \stdClass();

        $this->setExpectedException(\InvalidArgumentException::class);

        new PagedResultSet(new Integer(2), $activities);
    }
}
