<?php

namespace CultuurNet\UiTPASBeheer\UiTPAS\Price;

use CultuurNet\UiTPASBeheer\UiTPAS\Properties\AgeRange;
use CultuurNet\UiTPASBeheer\UiTPAS\Properties\VoucherType;
use ValueObjects\Money\Currency;
use ValueObjects\Money\CurrencyCode;
use ValueObjects\Money\Money;
use ValueObjects\Number\Integer;
use ValueObjects\Person\Age;
use ValueObjects\StringLiteral\StringLiteral;

class Price implements \JsonSerializable
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
        $kansenStatuut
    ) {
        $this->price = $price;
        $this->kansenStatuut = $kansenStatuut ? true : false;
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
     * @return AgeRange|null
     */
    public function getAgeRange()
    {
        return $this->ageRange;
    }

    /**
     * @param VoucherType $voucherType
     *
     * @return Price
     */
    public function withVoucherType(VoucherType $voucherType)
    {
        $offer = clone $this;
        $offer->voucherType = $voucherType;

        return $offer;
    }

    /**
     * @param AgeRange $ageRange
     *
     * @return Price
     */
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
            'price' => $priceInCents / 100,
            'kansenStatuut' => $this->isKansenStatuut(),
        ];

        if ($this->ageRange) {
            $jsonData['ageRange'] = $this->ageRange;
        }

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
        $priceInCents = Integer::fromNative($uitpasPrice->price * 100);
        $currency = new Currency(CurrencyCode::getByName('EUR'));

        $price = new static(
          new Money($priceInCents, $currency),
          $uitpasPrice->kansenStatuut
        );

        // Add age range if it was given.
        $uitpasAgeRange = $uitpasPrice->ageRange;
        $from = $uitpasAgeRange->ageFrom ? Age::fromNative($uitpasAgeRange->ageFrom) : null;
        $to = $uitpasAgeRange->ageTo ? Age::fromNative($uitpasAgeRange->ageTo) : null;
        if (!empty($from) || !empty($to)) {
            $ageRange = AgeRange::fromCultureFeedUitpasAgeRange($uitpasPrice->ageRange);
            $price = $price->withAgeRange($ageRange);
        }

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
