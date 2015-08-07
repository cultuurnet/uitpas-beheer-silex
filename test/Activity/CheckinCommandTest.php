<?php

namespace CultuurNet\UiTPASBeheer\Activity;

class CheckinCommandTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_returns_the_cdbid_of_the_activity_that_should_be_checked_in()
    {
        $command = new CheckinCommand(new Cdbid('aaaaaaaa-bbbb-cccc-dddd-eeeeeeeeeeee'));
        $expectedCdbid = new Cdbid('aaaaaaaa-bbbb-cccc-dddd-eeeeeeeeeeee');

        $this->assertEquals($expectedCdbid, $command->getEventCdbid());
    }
}
