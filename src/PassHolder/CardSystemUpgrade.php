<?php
/**
 * @file
 */

namespace CultuurNet\UiTPASBeheer\PassHolder;

use CultuurNet\UiTPASBeheer\CardSystem\Properties\CardSystemId;
use CultuurNet\UiTPASBeheer\KansenStatuut\KansenStatuut;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASNumber;

class CardSystemUpgrade
{
    /**
     * @var UiTPASNumber|null
     */
    private $newUiTPAS;

    /**
     * @var CardSystemId|null
     */
    private $cardSystemId;

    /**
     * private VoucherNumber|null
     */
    private $voucherNumber;

    /**
     * @var KansenStatuut|null
     */
    private $kansenStatuut;

    /**
     * Intentionally made private.
     * @see withNewUiTPAS()
     * @see withoutNewUiTPAS()
     */
    private function __construct()
    {

    }

    /**
     * @param UiTPASNumber $newUiTPAS
     * @return CardSystemUpgrade
     */
    public static function withNewUiTPAS(
        UiTPASNumber $newUiTPAS,
        KansenStatuut $kansenStatuut = null
    ) {
        $upgrade = new self();
        $upgrade->newUiTPAS = $newUiTPAS;
        $upgrade->kansenStatuut = $kansenStatuut;

        return $upgrade;
    }

    /**
     * @param CardSystemId $cardSystemId
     * @return CardSystemUpgrade
     */
    public static function withoutNewUiTPAS(CardSystemId $cardSystemId)
    {
        $upgrade = new self();
        $upgrade->cardSystemId = $cardSystemId;

        return $upgrade;
    }

    /**
     * @return UiTPASNumber|null
     */
    public function getNewUiTPAS()
    {
        return $this->newUiTPAS;
    }

    /**
     * @return CardSystemId|null
     */
    public function getCardSystemId()
    {
        return $this->cardSystemId;
    }

    /**
     * @param VoucherNumber $voucherNumber
     *
     * @return static
     */
    public function withVoucherNumber(VoucherNumber $voucherNumber)
    {
        $c = clone $this;
        $c->voucherNumber = $voucherNumber;
        return $c;
    }

    /**
     * @return VoucherNumber|null
     */
    public function getVoucherNumber()
    {
        return $this->voucherNumber;
    }

    /**
     * @return KansenStatuut|null
     */
    public function getKansenStatuut()
    {
        return $this->kansenStatuut;
    }
}
