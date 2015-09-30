<?php

namespace CultuurNet\UiTPASBeheer\UiTPAS\Registration;

class RegistrationTest extends \PHPUnit_Framework_TestCase
{
    use RegistrationTestDataTrait;

    /**
     * @test
     */
    public function it_returns_all_properties()
    {
        $minimal = $this->getMinimalRegistration();
        $complete = $this->getCompleteRegistration();

        $this->assertEquals(
            $this->getPassHolderUid(),
            $minimal->getPassHolderUid()
        );

        $this->assertEquals(
            $this->getReason(),
            $minimal->getReason()
        );

        $this->assertNull(
            $minimal->getKansenStatuut()
        );

        $this->assertNull(
            $minimal->getVoucherNumber()
        );

        $this->assertEquals(
            $this->getKansenStatuut(),
            $complete->getKansenStatuut()
        );

        $this->assertEquals(
            $this->getVoucherNumber(),
            $complete->getVoucherNumber()
        );
    }
}
