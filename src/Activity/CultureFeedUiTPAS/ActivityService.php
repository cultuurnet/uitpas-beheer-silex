<?php

namespace CultuurNet\UiTPASBeheer\Activity\CultureFeedUiTPAS;

use CultuurNet\UiTPASBeheer\Activity\Activity;
use CultuurNet\UiTPASBeheer\Activity\ActivityNotFoundException;
use CultuurNet\UiTPASBeheer\Activity\ActivityServiceInterface;
use CultuurNet\UiTPASBeheer\Activity\Cdbid;
use CultuurNet\UiTPASBeheer\Activity\CheckinConstraint;
use CultuurNet\UiTPASBeheer\Activity\PagedResultSet;
use CultuurNet\UiTPASBeheer\Activity\TicketSale\PriceClass;
use CultuurNet\UiTPASBeheer\Activity\TicketSale\Prices;
use CultuurNet\UiTPASBeheer\Activity\TicketSale\SalesInformation;
use CultuurNet\UiTPASBeheer\Activity\TicketSale\Tariff;
use CultuurNet\UiTPASBeheer\Activity\TicketSale\TariffType;
use CultuurNet\UiTPASBeheer\Counter\CounterAwareUitpasService;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASNumber;
use ValueObjects\DateTime\Date;
use ValueObjects\DateTime\DateTime;
use ValueObjects\Number\Integer;
use ValueObjects\Number\Real;
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

        $activities = array_map(
            function (\CultureFeed_Uitpas_Event_CultureEvent $event) {
                return Activity::fromCultureFeedUitpasEvent($event);
            },
            $result->objects
        );

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
        $searchOptions->uitpasNumber = $uitpasNumber->toNative();
        $searchOptions->balieConsumerKey = $this->getCounterConsumerKey();

        $result = $this->getUitpasService()->searchEvents($searchOptions);

        if ($result->total !== 1) {
            throw new ActivityNotFoundException($eventCdbid);
        }

        /* @var \CultureFeed_Uitpas_Event_CultureEvent $event */
        $event = array_values($result->objects)[0];

        $activity = Activity::fromCultureFeedUitpasEvent($event);

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
}
