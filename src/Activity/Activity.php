<?php

namespace CultuurNet\UiTPASBeheer\Activity;

use CultureFeed_Uitpas_Event_CultureEvent;
use CultureFeed_Cdb_Item_Event;
use ValueObjects\StringLiteral\StringLiteral;

class Activity implements \JsonSerializable
{
    /**
     * @var StringLiteral
     */
    protected $id;

    /**
     * @var StringLiteral
     */
    protected $title;

    /**
     * @var StringLiteral
     */
    protected $description;

    /**
     * @var StringLiteral
     *
     * Textual indication of when the activity is occurring.
     */
    protected $when;

    /**
     * @var CheckinConstraint
     */
    protected $checkinConstraint;

    /**
     * @param StringLiteral $id
     * @param StringLiteral $title
     * @param CheckinConstraint $checkinConstraint
     */
    public function __construct(
        StringLiteral $id,
        StringLiteral $title,
        CheckinConstraint $checkinConstraint
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->checkinConstraint = $checkinConstraint;
        $this->description = new StringLiteral('');
        $this->when = new StringLiteral('');
    }

    /**
     * @param StringLiteral $description
     * @return Activity
     */
    public function withDescription(StringLiteral $description)
    {
        $c = clone $this;
        $c->description = $description;
        return $c;
    }

    /**
     * @param StringLiteral $when
     * @return Activity
     */
    public function withWhen(StringLiteral $when)
    {
        $c = clone $this;
        $c->when = $when;
        return $c;
    }

    /**
     * @return StringLiteral
     */
    public function getWhen()
    {
        return $this->when;
    }

    /**
     * @return StringLiteral
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return StringLiteral
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return StringLiteral
     */
    public function getTitle()
    {
        return $this->title;
    }

    public function getCheckinConstraint()
    {
        return $this->checkinConstraint;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            'id' => $this->id->toNative(),
            'title' => $this->title->toNative(),
            'description' => $this->description->toNative(),
            'when' => $this->when->toNative(),
            'checkinConstraint' => $this->checkinConstraint->jsonSerialize(),
        ];
    }
}
