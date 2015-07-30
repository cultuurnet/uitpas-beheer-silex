<?php

namespace CultuurNet\UiTPASBeheer\UiTPAS;

use ValueObjects\StringLiteral\StringLiteral;

class UiTPAS implements \JsonSerializable
{
    /**
     * @var UiTPASNumber
     */
    protected $number;

    /**
     * @var UiTPASStatus
     */
    protected $status;

    /**
     * @var StringLiteral|null
     */
    protected $city;

    /**
     * @param UiTPASNumber $number
     * @param UiTPASStatus $status
     */
    public function __construct(
        UiTPASNumber $number,
        UiTPASStatus $status
    ) {
        $this->number = $number;
        $this->status = $status;
    }

    /**
     * @param StringLiteral $city
     * @return UiTPAS
     */
    public function withCity(StringLiteral $city)
    {
        $c = clone $this;
        $c->city = $city;
        return $c;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        $data = [
            'number' => $this->number->toNative(),
            'kansenStatuut' => $this->number->hasKansenStatuut(),
            'status' => $this->status->toNative(),
        ];

        if (!is_null($this->city)) {
            $data['city'] = $this->city->toNative();
        }

        return $data;
    }

    /**
     * @param \CultureFeed_Uitpas_Passholder_Card $cfCard
     * @return static
     */
    public static function fromCultureFeedPassHolderCard(\CultureFeed_Uitpas_Passholder_Card $cfCard)
    {
        $number = new UiTPASNumber($cfCard->uitpasNumber);
        $status = UiTPASStatus::get($cfCard->status);

        $card = new static($number, $status);

        if (!empty($cfCard->city)) {
            $card = $card->withCity(new StringLiteral($cfCard->city));
        }

        return $card;
    }
}
