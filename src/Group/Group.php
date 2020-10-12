<?php

namespace CultuurNet\UiTPASBeheer\Group;

use CultureFeed_Uitpas_GroupPass;
use JsonSerializable;
use ValueObjects\Number\Integer;
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
     * End date timestamp.
     *
     * @var Integer|null
     */
    private $endDate;

    public function __construct(
        StringLiteral $name,
        Natural $availableTickets,
        Integer $endDate = null
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
        $endDate = $groupPass->endDate ? new Integer($groupPass->endDate) : null;

        return new self(
            new StringLiteral($groupPass->name),
            new Natural($groupPass->availableTickets),
            $endDate
        );
    }

    /**
     * @inheritdoc
     */
    public function jsonSerialize()
    {
        return array_filter([
            'name' => $this->name->toNative(),
            'availableTickets' => $this->availableTickets->toNative(),
            'endDate' => $this->endDate ? $this->endDate->toNative() : null,
        ]);
    }
}
