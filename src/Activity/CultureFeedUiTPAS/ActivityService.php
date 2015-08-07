<?php

namespace CultuurNet\UiTPASBeheer\Activity\CultureFeedUiTPAS;

use CultuurNet\UiTPASBeheer\Activity\Activity;
use CultuurNet\UiTPASBeheer\Activity\ActivityNotFoundException;
use CultuurNet\UiTPASBeheer\Activity\ActivityServiceInterface;
use CultuurNet\UiTPASBeheer\Activity\Cdbid;
use CultuurNet\UiTPASBeheer\Activity\CheckinConstraint;
use CultuurNet\UiTPASBeheer\Activity\PagedResultSet;
use CultuurNet\UiTPASBeheer\Counter\CounterAwareUitpasService;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASNumber;
use ValueObjects\DateTime\Date;
use ValueObjects\DateTime\DateTime;
use ValueObjects\Number\Integer;
use ValueObjects\StringLiteral\StringLiteral;

class ActivityService extends CounterAwareUitpasService implements ActivityServiceInterface
{
    /**
     * @inheritdoc
     */
    public function search($search)
    {
        $searchOptions = $this->buildSearchOptions($search);

        $result = $this->getUitpasService()->searchEvents($searchOptions);

        $activities = $this->createActivities($result->objects);

        return new PagedResultSet(
            new Integer($result->total),
            $activities
        );
    }

    /**
     * @inheritdoc
     */
    public function get(UiTPASNumber $uitpasNumber, Cdbid $eventCdbid)
    {
        $searchOptions = new \CultureFeed_Uitpas_Event_Query_SearchEventsOptions();
        $searchOptions->cdbid = $eventCdbid->toNative();
        $searchOptions->uitpasNumber = $uitpasNumber->getNumber();
        $searchOptions->balieConsumerKey = $this->getCounterConsumerKey();

        $result = $this->getUitpasService()->searchEvents($searchOptions);

        if ($result->total !== 1) {
            throw new ActivityNotFoundException($eventCdbid);
        }

        /* @var \CultureFeed_Uitpas_Event_CultureEvent $event */
        $event = array_values($result->objects)[0];

        $activity = $this->createActivity($event);

        return $activity;

    }

    /**
     * @param SearchOptionsBuilderInterface $search
     * @return \CultureFeed_Uitpas_Event_Query_SearchEventsOptions
     */
    private function buildSearchOptions(SearchOptionsBuilderInterface $search)
    {
        $searchOptions = $search->build();
        $searchOptions->balieConsumerKey = $this->getCounterConsumerKey();

        return $searchOptions;
    }

    /**
     * @param \CultureFeed_Uitpas_Event_CultureEvent[] $events
     *
     * @return Activity[]
     */
    private function createActivities(array $events)
    {
        return array_map(
            function (\CultureFeed_Uitpas_Event_CultureEvent $event) {
                return $this->createActivity($event);
            },
            $events
        );
    }

    /**
     * @param \CultureFeed_Uitpas_Event_CultureEvent $event
     * @return \CultuurNet\UiTPASBeheer\Activity\Activity
     */
    private function createActivity(\CultureFeed_Uitpas_Event_CultureEvent $event)
    {
        // For the moment the api has a bug that can return empty checkin
        // constraint dates for events in the past.
        $checkinStartDate = \DateTime::createFromFormat('U', 0);
        if ($event->checkinStartDate) {
            $checkinStartDate = \DateTime::createFromFormat(\DateTime::W3C, $event->checkinStartDate);
        }
        $checkinEndDate = \DateTime::createFromFormat('U', 0);
        if ($event->checkinEndDate) {
            // Another bug returns the end date with microseconds.
            $checkinEndDate = \DateTime::createFromFormat('Y-m-d\TH:i:s.uP', $event->checkinEndDate);
        }

        $checkinConstraint = new CheckinConstraint(
            (bool) $event->checkinAllowed,
            DateTime::fromNativeDateTime($checkinStartDate),
            DateTime::fromNativeDateTime($checkinEndDate)
        );

        if (!$event->checkinAllowed && $event->checkinConstraintReason) {
            $checkinConstraint = $checkinConstraint->withReason(
                new StringLiteral((string) $event->checkinConstraintReason)
            );
        }

        return new Activity(
            new StringLiteral((string) $event->cdbid),
            new StringLiteral((string) $event->title),
            $checkinConstraint
        );
    }
}
