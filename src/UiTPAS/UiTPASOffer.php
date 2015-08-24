<?php

namespace CultuurNet\UiTPASBeheer\UiTPAS;

use CultuurNet\UiTPASBeheer\UiTPAS\properties\AgeRange;
use CultuurNet\UiTPASBeheer\UiTPAS\properties\VoucherType;
use ValueObjects\Money\Currency;
use ValueObjects\Money\CurrencyCode;
use ValueObjects\Money\Money;
use ValueObjects\Number\Integer;
use ValueObjects\StringLiteral\StringLiteral;

class UiTPASOffer implements \JsonSerializable
{
    /**
     * @var Money
     */
    protected $price;

    /**
     * @var boolean
     */
    protected $kansenstatuut;

    /**
     * @var VoucherType
     */
    protected $voucherType;

    /**
     * @var AgeRange
     */
    protected $ageRange;

    public function __construct(
        Money $price,
        $kansenstatuut = false
    ) {
        $this->price = $price;
        $this->kansenstatuut = $kansenstatuut;
    }

    /**
     * @return Money
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @return boolean
     */
    public function isKansenstatuut()
    {
        return $this->kansenstatuut;
    }

    /**
     * @return VoucherType|null
     */
    public function getVoucherType()
    {
        return $this->voucherType;
    }

    /**
     * @return AgeRange
     */
    public function getAgeRange()
    {
        return $this->ageRange;
    }

    /**
     * @param VoucherType $voucherType
     *
     * @return UiTPASOffer
     */
    public function withVoucherType(VoucherType $voucherType)
    {
        $offer = clone $this;
        $offer->voucherType = $voucherType;

        return $offer;
    }

    public function withAgeRange(AgeRange $ageRange)
    {
        $offer = clone $this;
        $offer->ageRange = $ageRange;

        return $offer;
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize()
    {
        $priceInCents = $this->getPrice()->getAmount()->toNative();

        $jsonData = [
            'price' => $priceInCents,
            'kansenstatuut' => $this->isKansenstatuut(),
            'ageRange' => $this->ageRange,
        ];

        if ($this->voucherType) {
            $jsonData['voucherType'] = $this->voucherType;
        };

        return $jsonData;
    }

    public function equals(UiTPASOffer $uitpasOffer)
    {
        return $this == $uitpasOffer;
    }

    /**
     * @param \CultureFeed_Uitpas_Passholder_UitpasPrice
     * @return static
     */
    public static function fromCultureFeedUiTPASPrice(\CultureFeed_Uitpas_Passholder_UitpasPrice $uitpasPrice)
    {
        // UiTPAS returns the price as a float, convert it to cents to use as money
        $priceInCents = Integer::fromNative($uitpasPrice->price*100);
        $currency = new Currency(CurrencyCode::EUR());

        $offer = new static(
            new Money($priceInCents, $currency),
            $uitpasPrice->kansenStatuut
        );

        $ageRange = AgeRange::fromCultureFeedUitpasAgeRange($uitpasPrice->ageRange);
        $offer = $offer->withAgeRange($ageRange);

        if ($uitpasPrice->voucherType) {
            $voucherType = new VoucherType(
                new StringLiteral($uitpasPrice->voucherType->name),
                new StringLiteral($uitpasPrice->voucherType->prefix)
            );
            $offer = $offer->withVoucherType($voucherType);
        }

        return $offer;

    }
}
