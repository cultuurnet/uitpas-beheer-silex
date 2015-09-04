<?php

namespace CultuurNet\UiTPASBeheer\CardSystem;

use CultuurNet\UiTPASBeheer\CardSystem\Properties\CardSystemId;
use ValueObjects\StringLiteral\StringLiteral;

final class CardSystem implements \JsonSerializable
{
    /**
     * @var CardSystemId
     */
    private $id;

    /**
     * @var StringLiteral
     */
    private $name;

    /**
     * @param CardSystemId $id
     * @param StringLiteral $name
     */
    public function __construct(
        CardSystemId $id,
        StringLiteral $name
    ) {
        $this->id = $id;
        $this->name = $name;
    }

    /**
     * @return CardSystemId
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return StringLiteral
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            'id' => $this->id->toNative(),
            'name' => $this->name->toNative(),
        ];
    }

    /**
     * @param \CultureFeed_Uitpas_CardSystem $cfCardSystem
     * @return CardSystem
     */
    public static function fromCultureFeedCardSystem(\CultureFeed_Uitpas_CardSystem $cfCardSystem)
    {
        return new CardSystem(
            new CardSystemId((string) $cfCardSystem->id),
            new StringLiteral((string) $cfCardSystem->name)
        );
    }
}
