<?php
/**
 * @file
 */

namespace CultuurNet\UiTPASBeheer\Group;

use CultureFeed_Uitpas_GroupPass;
use JsonSerializable;
use ValueObjects\Number\Natural;
use ValueObjects\StringLiteral\StringLiteral;

class Group implements JsonSerializable
{
    /**
     * @var StringLiteral
     */
    private $name;

    /**
     * @var Natural
     */
    private $availableTickets;

    /**
     * @param StringLiteral $name
     * @param Natural $availableTickets
     */
    public function __construct(
        StringLiteral $name,
        Natural $availableTickets
    ) {
        $this->name = $name;
        $this->availableTickets = $availableTickets;
    }

    /**
     * @param CultureFeed_Uitpas_GroupPass $groupPass
     * @return Group
     */
    public static function fromCultureFeedGroupPass(
        CultureFeed_Uitpas_GroupPass $groupPass
    ) {
        return new self(
            new StringLiteral($groupPass->name),
            new Natural($groupPass->availableTickets)
        );
    }

    /**
     * @inheritdoc
     */
    public function jsonSerialize()
    {
        return [
            'name' => $this->name->toNative(),
            'availableTickets' => $this->availableTickets->toNative(),
        ];
    }
}
