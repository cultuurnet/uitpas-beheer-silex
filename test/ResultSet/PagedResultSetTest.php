<?php

namespace CultuurNet\UiTPASBeheer\ResultSet;

use ValueObjects\Number\Integer;

class PagedResultSetTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_refuses_a_total_lower_than_the_amount_of_current_results()
    {
        $results = [
            new \stdClass(),
            new \stdClass(),
        ];

        $this->setExpectedException(\LogicException::class);

        new MockPagedResultSet(new Integer(1), $results);
    }
}
