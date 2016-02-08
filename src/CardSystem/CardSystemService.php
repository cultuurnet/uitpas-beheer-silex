<?php
/**
 * @file
 */

namespace CultuurNet\UiTPASBeheer\CardSystem;

use CultuurNet\UiTPASBeheer\CardSystem\Price\Inquiry;
use CultuurNet\UiTPASBeheer\Counter\CounterAwareUitpasService;
use CultuurNet\UiTPASBeheer\UiTPAS\Price\Price;

class CardSystemService extends CounterAwareUitpasService implements CardSystemServiceInterface
{
    /**
     * @inheritdoc
     */
    public function getPrice(Inquiry $inquiry)
    {
        $dateOfBirth = $postalCode = $voucherNumber = null;

        if (!is_null($inquiry->getDateOfBirth())) {
            $dateOfBirth = $inquiry->getDateOfBirth()
                ->toNativeDateTime()
                ->getTimestamp();
        }

        if (!is_null($inquiry->getPostalCode())) {
            $postalCode = $inquiry->getPostalCode()
                ->toNative();
        }

        if (!is_null($inquiry->getVoucherNumber())) {
            $voucherNumber = $inquiry->getVoucherNumber()
                ->toNative();
        }

        $cfPrice = $this->getUitpasService()->getPriceForUpgrade(
            $inquiry->getCardSystemId()->toNative(),
            $dateOfBirth,
            $postalCode,
            $voucherNumber,
            $this->getCounterConsumerKey()
        );

        return Price::fromCultureFeedUiTPASPrice($cfPrice);
    }
}
