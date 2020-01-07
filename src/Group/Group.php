<?php
/**
 * @file
 */

namespace CultuurNet\UiTPASBeheer\Group;

use CultureFeed_Uitpas_GroupPass;
use JsonSerializable;
use ValueObjects\Number\Integer;
use ValueObjects\Number\Natural;
use ValueObjects\StringLiteral\StringLiteral;
use ValueObjects\DateTime\DateTimeWithTimeZone;

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
     * End date timestamp.
     *
     * @var Integer
     */
    private $endDate;

    /**
     * @param StringLiteral $name
     * @param Natural $availableTickets
     * @param Integer $endDate
     */
    public function __construct(
        StringLiteral $name,
        Natural $availableTickets,
        Integer $endDate
    ) {
        $this->name = $name;
        $this->availableTickets = $availableTickets;
        $this->endDate = $endDate;
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
            new Natural($groupPass->availableTickets),
            new Integer($groupPass->endDate)
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
            'endDate' => $this->endDate->toNative(),
        ];
    }
}
