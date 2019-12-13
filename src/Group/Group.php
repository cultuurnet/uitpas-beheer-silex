<?php
/**
 * @file
 */

namespace CultuurNet\UiTPASBeheer\Group;

use CultureFeed_Uitpas_GroupPass;
use JsonSerializable;
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
     * @var DateTimeWithTimeZone
     */
    private $endDate;

    /**
     * @param StringLiteral $name
     * @param Natural $availableTickets
     * @param DateTimeWithTimeZone $endDate
     */
    public function __construct(
        StringLiteral $name,
        Natural $availableTickets,
        DateTimeWithTimeZone $endDate
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

        $date = \DateTime::createFromFormat('U',$groupPass->endDate);
        $date->setTimezone(new \DateTimeZone('Europe/London'));
        $date = DateTimeWithTimeZone::fromNativeDateTime($date);

        return new self(
            new StringLiteral($groupPass->name),
            new Natural($groupPass->availableTickets),
            $date
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
            'endDate' => $this->endDate->toNativeDateTime(),
        ];
    }
}
