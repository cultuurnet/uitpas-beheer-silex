<?php

namespace CultuurNet\UiTPASBeheer\UiTPAS;

use CultuurNet\UiTPASBeheer\Counter\CounterAwareUitpasService;
use CultuurNet\UiTPASBeheer\Exception\ReadableCodeResponseException;
use CultuurNet\UiTPASBeheer\UiTPAS\Price\Inquiry;
use CultuurNet\UiTPASBeheer\UiTPAS\Price\Price;
use CultuurNet\UiTPASBeheer\UiTPAS\Registration\Registration;

class UiTPASService extends CounterAwareUitpasService implements UiTPASServiceInterface
{
    /**
     * @param UiTPASNumber $uitpasNumber
     */
    public function block(UiTPASNumber $uitpasNumber)
    {
        $this->getUitpasService()->blockUitpas(
            $uitpasNumber->toNative(),
            $this->getCounterConsumerKey()
        );
    }

    /**
     * @param UiTPASNumber $uitpasNumber
     * @return UiTPAS
     */
    public function get(UiTPASNumber $uitpasNumber)
    {
        $uitpasQuery = new \CultureFeed_Uitpas_CardInfoQuery();
        $uitpasQuery->uitpasNumber = $uitpasNumber->toNative();
        $uitpasQuery->balieConsumerKey = $this->getCounterConsumerKey();

        return UiTPAS::fromCultureFeedCardInfo(
            $this->getUitpasService()->getCard($uitpasQuery)
        );
    }

    /**
     * @param UiTPASNumber $uitpasNumber
     * @param Registration $registration
     */
    public function register(UiTPASNumber $uitpasNumber, Registration $registration)
    {
        $options = new \CultureFeed_Uitpas_Passholder_Query_RegisterUitpasOptions();
        $options->balieConsumerKey = $this->getCounterConsumerKey();
        $options->uitpasNumber = $uitpasNumber->toNative();
        $options->uid = $registration->getPassHolderUid()->toNative();
        $options->reason = $registration->getReason()->toNative();

        if (!is_null($registration->getKansenStatuut())) {
            $options->kansenStatuutEndDate = $registration
                ->getKansenStatuut()
                ->getEndDate()
                ->toNativeDateTime()
                ->getTimestamp();
        }

        if (!is_null($registration->getVoucherNumber())) {
            $options->voucherNumber = $registration->getVoucherNumber()->toNative();
        }

        $this->getUitpasService()->registerUitpas($options);
    }

    /**
     * @param Inquiry $inquiry
     *
     * @return Price
     *
     * @throws ReadableCodeResponseException
     *   When an UiTPAS API error occurred.
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

        $cfPrice = $this->getUitpasService()->getPriceByUitpas(
            $inquiry->getUiTPASNumber()->toNative(),
            $inquiry->getReason()->toNative(),
            $dateOfBirth,
            $postalCode,
            $voucherNumber,
            $this->getCounterConsumerKey()
        );

        return Price::fromCultureFeedUiTPASPrice($cfPrice);
    }
}
