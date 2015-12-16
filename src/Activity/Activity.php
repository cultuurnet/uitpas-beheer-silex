<?php

namespace CultuurNet\UiTPASBeheer\Activity;

use CultureFeed_Uitpas_Event_CultureEvent;
use CultureFeed_Cdb_Item_Event;
use CultuurNet\UiTPASBeheer\Activity\SalesInformation\SalesInformation;
use CultuurNet\UiTPASBeheer\Activity\Specifications\IsFree;
use CultuurNet\UiTPASBeheer\Properties\Location;
use ValueObjects\Number\Integer;
use ValueObjects\Number\Natural;
use ValueObjects\StringLiteral\StringLiteral;

/**
 * Class Activity
 * @package CultuurNet\UiTPASBeheer\Activity
 */
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
     * @var StringLiteral|null
     */
    protected $description;

    /**
     * @var StringLiteral|null
     *
     * Textual indication of when the activity is occurring.
     */
    protected $when;

    /**
     * @var Location|null
     *
     * Object containing information about where the activity is occurring.
     */
    protected $location;

    /**
     * @var Natural|null
     *
     * Minimum age target group of the activity.
     */
    protected $minimumAge;

    /**
     * @var CheckinConstraint
     */
    protected $checkinConstraint;

    /**
     * @var SalesInformation|null
     */
    protected $salesInformation;

    /**
     * @var Integer
     */
    protected $points;

    /**
     * @param StringLiteral $id
     * @param StringLiteral $title
     * @param CheckinConstraint $checkinConstraint
     * @param \ValueObjects\Number\Integer $points
     */
    public function __construct(
        StringLiteral $id,
        StringLiteral $title,
        CheckinConstraint $checkinConstraint,
        Integer $points
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->checkinConstraint = $checkinConstraint;
        $this->points = $points;
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
     * @param Location $location
     * @return Activity
     */
    public function withLocation(Location $location)
    {
        $c = clone $this;
        $c->location = $location;
        return $c;
    }

    /**
     * @param Natural $age
     * @return Activity
     */
    public function withMinimumAge(Natural $age)
    {
        $c = clone $this;
        $c->minimumAge = $age;
        return $c;
    }

    /**
     * @param SalesInformation $salesInformation
     * @return Activity
     */
    public function withSalesInformation(SalesInformation $salesInformation)
    {
        $c = clone $this;
        $c->salesInformation = $salesInformation;
        return $c;
    }

    /**
     * @return StringLiteral|null
     */
    public function getWhen()
    {
        return $this->when;
    }

    /**
     * @return StringLiteral|null
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return Location|null
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @return Natural|null
     */
    public function getMinimumAge()
    {
        return $this->minimumAge;
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
     * @return CheckinConstraint
     */
    public function getCheckinConstraint()
    {
        return $this->checkinConstraint;
    }

    /**
     * @return SalesInformation|null
     */
    public function getSalesInformation()
    {
        return $this->salesInformation;
    }

    /**
     * @return Integer
     */
    public function getPoints()
    {
        return $this->points;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        $data = [
            'id' => $this->id->toNative(),
            'title' => $this->title->toNative(),
            'checkinConstraint' => $this->checkinConstraint,
            'points' => $this->points->toNative(),
            'free' => IsFree::isSatisfiedBy($this),
        ];

        if (!is_null($this->description)) {
            $data['description'] = $this->description->toNative();
        }

        if (!is_null($this->when)) {
            $data['when'] = $this->when->toNative();
        }

        if (!is_null($this->salesInformation) && !$data['free']) {
            $data['sales'] = $this->salesInformation->jsonSerialize();
        }

        if (!is_null($this->location)) {
            $data['where'] = $this->location->jsonSerialize();
        }

        if (!is_null($this->minimumAge)) {
            $data['age'] = $this->minimumAge->toNative();
        }

        return $data;
    }

    /**
     * @param \CultureFeed_Uitpas_Event_CultureEvent $event
     * @return Activity
     */
    public static function fromCultureFeedUitpasEvent(\CultureFeed_Uitpas_Event_CultureEvent $event)
    {
        $activity = new Activity(
            StringLiteral::fromNative((string) $event->cdbid),
            StringLiteral::fromNative((string) $event->title),
            CheckinConstraint::fromCultureFeedUitpasEvent($event),
            Integer::fromNative((int) $event->numberOfPoints)
        );

        $salesInformation = SalesInformation::fromCultureFeedUitpasEvent($event);
        $activity = $activity->withSalesInformation($salesInformation);

        return $activity;
    }
}
