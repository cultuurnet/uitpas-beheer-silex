<?php

namespace CultuurNet\UiTPASBeheer\UiTPAS;

use CultuurNet\UiTPASBeheer\CardSystem\Properties\CardSystemId;
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
     * @var UiTPASType
     */
    protected $type;

    /**
     * @var CardSystemId
     */
    protected $cardSystemId;

    /**
     * @var StringLiteral|null
     */
    protected $city;

    /**
     * @param UiTPASNumber $number
     * @param UiTPASStatus $status
     * @param UiTPASType $type
     * @param CardSystemId $cardSystemId
     */
    public function __construct(
        UiTPASNumber $number,
        UiTPASStatus $status,
        UiTPASType $type,
        CardSystemId $cardSystemId
    ) {
        $this->number = $number;
        $this->status = $status;
        $this->type = $type;
        $this->cardSystemId = $cardSystemId;
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
            'type' => $this->type->toNative(),
            'cardSystemId' => $this->cardSystemId->toNative(),
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
        $type = UiTPASType::get($cfCard->type);
        $cardSystemId = new CardSystemId((string) $cfCard->cardSystemId);

        $card = new static(
            $number,
            $status,
            $type,
            $cardSystemId
        );

        if (!empty($cfCard->city)) {
            $card = $card->withCity(new StringLiteral($cfCard->city));
        }

        return $card;
    }
}
