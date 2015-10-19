<?php
/**
 * @file
 */

namespace CultuurNet\UiTPASBeheer\PassHolder\Search;

use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASNumber;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASNumberCollection;
use ValueObjects\Number\Integer;

class PagedResultSetTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_keeps_invalid_uitpas_numbers()
    {
        $set = new PagedResultSet(
            new Integer(0),
            []
        );

        $invalidUiTPASNumbers = (new UiTPASNumberCollection())
            ->with(new UiTPASNumber('3330047460116'));

        $set = $set->withInvalidUiTPASNumbers(
            $invalidUiTPASNumbers
        );

        $this->assertEquals(
            $invalidUiTPASNumbers,
            $set->getInvalidUitpasNumbers()
        );
    }
}
