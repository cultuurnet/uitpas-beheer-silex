<?php

namespace CultuurNet\UiTPASBeheer\Membership\Registration;

use CultuurNet\UiTPASBeheer\Membership\Association\Properties\AssociationId;
use ValueObjects\DateTime\Date;
use ValueObjects\DateTime\Month;
use ValueObjects\DateTime\MonthDay;
use ValueObjects\DateTime\Year;

class RegistrationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_can_store_and_return_all_necessary_registration_data()
    {
        $associationId = new AssociationId('1245-789');

        $registration = new Registration($associationId);

        $this->assertEquals($associationId, $registration->getAssociationId());
        $this->assertNull($registration->getEndDate());

        $endDate = new Date(
            new Year('2015'),
            Month::getByName('SEPTEMBER'),
            new MonthDay('1')
        );

        $registration = $registration->withEndDate($endDate);

        $this->assertEquals($endDate, $registration->getEndDate());
    }
}
