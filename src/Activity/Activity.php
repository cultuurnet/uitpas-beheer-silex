<?php

namespace CultuurNet\UiTPASBeheer\Activity;

use CultuurNet\UiTPASBeheer\Activity\SalesInformation\SalesInformation;
use CultuurNet\UiTPASBeheer\Activity\Specifications\IsFree;
use ValueObjects\Number\Integer;
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
     * @param StringLiteral $description
     * @param CheckinConstraint $checkinConstraint
     * @param Integer $points
     */
    public function __construct(
        StringLiteral $id,
        StringLiteral $title,
        StringLiteral $description,
        CheckinConstraint $checkinConstraint,
        Integer $points
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->description = $description;
        $this->checkinConstraint = $checkinConstraint;
        $this->points = $points;
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
     * @param StringLiteral $when
     */
    public function setWhen(StringLiteral $when)
    {
        $this->when = $when;
    }

    /**
     * @return StringLiteral|null
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
            StringLiteral::fromNative((string) $event->description),
            CheckinConstraint::fromCultureFeedUitpasEvent($event),
            Integer::fromNative((int) $event->numberOfPoints)
        );

        // Format calendar timestamps to formatted when value.
        if (!empty($event->calendar->timestamps)) {
            $formatter = new ActivityTimestampsFormatter();

            $when = new StringLiteral(
                (string) $formatter->format($event->calendar->timestamps)
            );

            $activity->setWhen($when);
        }

        $salesInformation = SalesInformation::fromCultureFeedUitpasEvent($event);
        $activity = $activity->withSalesInformation($salesInformation);

        return $activity;
    }
}
