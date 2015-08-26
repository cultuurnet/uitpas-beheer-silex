<?php

namespace CultuurNet\UiTPASBeheer\UiTPAS;

use CultuurNet\UiTPASBeheer\UiTPAS\properties\AgeRange;
use CultuurNet\UiTPASBeheer\UiTPAS\properties\VoucherType;
use ValueObjects\Money\Currency;
use ValueObjects\Money\CurrencyCode;
use ValueObjects\Money\Money;
use ValueObjects\Number\Integer;
use ValueObjects\StringLiteral\StringLiteral;

class UiTPASPrice implements \JsonSerializable
{
    /**
     * @var Money
     */
    protected $price;

    /**
     * @var boolean
     */
    protected $kansenStatuut;

    /**
     * @var VoucherType
     */
    protected $voucherType;

    /**
     * @var AgeRange
     */
    protected $ageRange;

    /**
     * @param Money $price
     * @param boolean $kansenStatuut
     * @param AgeRange $ageRange
     */
    public function __construct(
        Money $price,
        $kansenStatuut,
        AgeRange $ageRange
    ) {
        $this->price = $price;
        $this->kansenStatuut = $kansenStatuut ? true : false;
        $this->ageRange = $ageRange;
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
    public function isKansenStatuut()
    {
        return $this->kansenStatuut;
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
     * @return UiTPASPrice
     */
    public function withVoucherType(VoucherType $voucherType)
    {
        $offer = clone $this;
        $offer->voucherType = $voucherType;

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
            'kansenStatuut' => $this->isKansenStatuut(),
            'ageRange' => $this->ageRange,
        ];

        if ($this->voucherType) {
            $jsonData['voucherType'] = $this->voucherType;
        };

        return $jsonData;
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
        $ageRange = AgeRange::fromCultureFeedUitpasAgeRange($uitpasPrice->ageRange);

        $price = new static(
            new Money($priceInCents, $currency),
            $uitpasPrice->kansenStatuut,
            $ageRange
        );

        if ($uitpasPrice->voucherType) {
            $voucherType = new VoucherType(
                new StringLiteral($uitpasPrice->voucherType->name),
                new StringLiteral($uitpasPrice->voucherType->prefix)
            );
            $price = $price->withVoucherType($voucherType);
        }

        return $price;

    }
}
