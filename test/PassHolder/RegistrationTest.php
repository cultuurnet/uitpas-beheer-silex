<?php

namespace CultuurNet\UiTPASBeheer\PassHolder;

use CultuurNet\UiTPASBeheer\KansenStatuut\KansenStatuut;
use CultuurNet\UiTPASBeheer\PassHolder\Properties\Name;
use ValueObjects\DateTime\Date;

class RegistrationTest extends \PHPUnit_Framework_TestCase
{

    use PassHolderDataTrait;

    /**
     * @test
     */
    public function it_should_keep_track_off_all_registration_information()
    {
        $passholder = $this->getCompletePassHolder();

        /**
         * @var Registration $registration
         */
        $registration = new Registration($passholder);

        $registration = $registration
            ->withVoucherNumber(new VoucherNumber('v-to-the-oucher'))
            ->withKansenstatuut(new KansenStatuut(Date::now()));

        $this->assertNotNull($registration->getPassHolder());
        $this->assertNotNull($registration->getVoucherNumber());
        $this->assertNotNull($registration->getKansenStatuut());
    }
}
