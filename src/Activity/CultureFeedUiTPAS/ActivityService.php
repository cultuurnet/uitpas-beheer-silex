<?php

namespace CultuurNet\UiTPASBeheer\Activity\CultureFeedUiTPAS;

use CultuurNet\UiTPASBeheer\Activity\Activity;
use CultuurNet\UiTPASBeheer\Activity\ActivityServiceInterface;
use CultuurNet\UiTPASBeheer\Activity\PagedResultSet;
use CultuurNet\UiTPASBeheer\Counter\CounterAwareUitpasService;
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

    private function createActivity(\CultureFeed_Uitpas_Event_CultureEvent $event)
    {
        return new Activity(
            new StringLiteral((string) $event->cdbid),
            new StringLiteral((string) $event->title)
        );
    }
}
