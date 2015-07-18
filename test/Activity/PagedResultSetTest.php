<?php
/**
 * @file
 */

namespace CultuurNet\UiTPASBeheer\Activity;

use ValueObjects\Number\Integer;
use ValueObjects\StringLiteral\StringLiteral;

class PagedResultSetTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_refuses_a_total_lower_than_the_amount_of_current_results()
    {
        $activities = [];
        $activities[] = new Activity(
            new StringLiteral('activity-1'),
            new StringLiteral('Activity 1'),
            new StringLiteral('Description of activity 1'),
            new StringLiteral('Each Wednesday')
        );
        $activities[] = new Activity(
            new StringLiteral('activity-2'),
            new StringLiteral('Activity 2'),
            new StringLiteral('Description of activity 2'),
            new StringLiteral('Each Wednesday')
        );

        $this->setExpectedException(\LogicException::class);

        new PagedResultSet(new Integer(1), $activities);
    }
}
