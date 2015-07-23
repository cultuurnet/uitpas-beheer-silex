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
     * @param StringLiteral $id
     * @param StringLiteral $title
     * @param bool $checkinAllowed
     * @param string $checkinConstraintReason
     */
    public function __construct(
        StringLiteral $id,
        StringLiteral $title,
        $checkinAllowed,
        $checkinConstraintReason
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->checkinAllowed = $checkinAllowed;
        $this->checkinConstraintReason = $checkinConstraintReason;
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
            'checkin' => (object) array(
                'allowed' => $this->checkinAllowed,
                'reason' => $this->checkinConstraintReason,
            ),
        ];
    }
}
