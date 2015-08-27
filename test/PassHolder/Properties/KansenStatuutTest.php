<?php

namespace CultuurNet\UiTPASBeheer\PassHolder\Properties;

use ValueObjects\DateTime\Date;

class KansenstatuutTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_requires_an_end_date_and_optional_remarks()
    {
        $kansenstatuut = new KansenStatuut(Date::now());
        $kansenstatuut = $kansenstatuut->withRemarks(new Remarks('beep boop'));

        $this->assertInstanceOf(Date::class, $kansenstatuut->getEndDate());
        $this->assertInstanceOf(Remarks::class, $kansenstatuut->getRemarks());
    }
}
