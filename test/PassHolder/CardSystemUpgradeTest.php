<?php
/**
 * @file
 */

namespace CultuurNet\UiTPASBeheer\PassHolder;

use CultuurNet\UiTPASBeheer\CardSystem\Properties\CardSystemId;
use CultuurNet\UiTPASBeheer\KansenStatuut\KansenStatuut;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASNumber;
use ValueObjects\DateTime\Date;
use ValueObjects\DateTime\Month;
use ValueObjects\DateTime\MonthDay;
use ValueObjects\DateTime\Year;

class CardSystemUpgradeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function can_be_performed_with_a_new_uitpas()
    {
        $newUiTPAS = new UiTPASNumber('0930000420206');
        $upgrade = CardSystemUpgrade::withNewUiTPAS($newUiTPAS);

        $this->assertEquals($newUiTPAS, $upgrade->getNewUiTPAS());
        $this->assertNull($upgrade->getCardSystemId());
    }

    /**
     * @test
     */
    public function can_be_performed_without_a_new_uitpas()
    {
        $cardSystemId = new CardSystemId('1');
        $upgrade = CardSystemUpgrade::withoutNewUiTPAS($cardSystemId);

        $this->assertEquals($cardSystemId, $upgrade->getCardSystemId());
        $this->assertEquals(null, $upgrade->getNewUiTPAS());
    }

    /**
     * @test
     */
    public function optionally_uses_a_voucher_to_get_a_price_discount()
    {
        $upgrade = CardSystemUpgrade::withoutNewUiTPAS(new CardSystemId('1'));

        $this->assertNull($upgrade->getVoucherNumber());

        $voucherNumber = new VoucherNumber('free ticket to ride');

        $upgradeWithVoucher = $upgrade->withVoucherNumber($voucherNumber);

        $this->assertEquals($voucherNumber, $upgradeWithVoucher->getVoucherNumber());
        $this->assertNull($upgrade->getVoucherNumber());
    }

    /**
     * @test
     */
    public function optionally_includes_a_kansenstatuut_when_performed_with_a_new_uitpas()
    {
        $newUiTPAS = new UiTPASNumber('0930000420206');
        $kansenStatuutEndDate = new Date(
            new Year('2016'),
            Month::APRIL(),
            new MonthDay(1)
        );
        $kansenStatuut = new KansenStatuut($kansenStatuutEndDate);
        $upgrade = CardSystemUpgrade::withNewUiTPAS($newUiTPAS, $kansenStatuut);

        $this->assertEquals($kansenStatuut, $upgrade->getKansenStatuut());
    }
}
