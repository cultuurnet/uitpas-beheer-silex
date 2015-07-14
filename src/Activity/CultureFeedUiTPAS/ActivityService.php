<?php

namespace CultuurNet\UiTPASBeheer\Activity\CultureFeedUiTPAS;

use CultureFeed_Uitpas;
use CultuurNet\Search\Guzzle\Service;
use CultuurNet\Search\Parameter\BooleanParameter;
use CultuurNet\Search\Parameter\Group;
use CultuurNet\Search\Parameter\Query;
use CultuurNet\Search\ServiceInterface;
use CultuurNet\UiTPASBeheer\Activity\Activity;
use CultuurNet\UiTPASBeheer\Activity\ActivityServiceInterface;
use CultuurNet\UiTPASBeheer\Counter\CounterAwareUitpasService;
use CultuurNet\UiTPASBeheer\Counter\CounterConsumerKey;

class ActivityService extends CounterAwareUitpasService implements ActivityServiceInterface
{
    /**
     * @var Service
     */
    private $searchService;

    /**
     * @param CultureFeed_Uitpas $uitpasService
     * @param CounterConsumerKey $counterConsumerKey
     * @param ServiceInterface $searchService
     */
    public function __construct(
        CultureFeed_Uitpas $uitpasService,
        CounterConsumerKey $counterConsumerKey,
        ServiceInterface $searchService
    ) {
        parent::__construct($uitpasService, $counterConsumerKey);

        $this->searchService = $searchService;
    }

    /**
     * @inheritdoc
     */
    public function search($search)
    {
        $searchOptions = $this->buildSearchOptions($search);

        $result = $this->getUitpasService()->searchEvents($searchOptions);

        return $this->combineWithFullEventData($result);
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
     * @param \CultureFeed_ResultSet $result
     *
     * @return Activity[]
     */
    private function combineWithFullEventData(\CultureFeed_ResultSet $result)
    {
        return array_map(
            function (\CultureFeed_Uitpas_Event_CultureEvent $uitpasEventData) {
                try {
                    $fullEventData = $this->getFullEventData(
                        $uitpasEventData->cdbid
                    );
                } catch (\RuntimeException $e) {
                    $fullEventData = null;
                }

                return Activity::fromCultureFeedUitpasAndCdbEvent(
                    $uitpasEventData,
                    $fullEventData
                );
            },
            $result->objects
        );
    }

    /**
     * @param string $id
     *
     * @return \CultureFeed_Cdb_Item_Event
     */
    private function getFullEventData($id)
    {
        if (!is_string($id)) {
            throw new \InvalidArgumentException(
                'Expected $id to be a string, received value of type ' . gettype($id)
            );
        }

        $parameters = [
            new BooleanParameter('past', true),
            new Group(),
            new Query('cdbid:"' . $id . '"')
        ];

        $result = $this->searchService->search($parameters);

        if (1 !== $result->getCurrentCount()) {
            throw new \RuntimeException(
                'Expected exactly 1 event to be returned, received ' . $result->getCurrentCount() . 'results'
            );
        }

        $items = $result->getItems();

        /* @var \CultuurNet\Search\ActivityStatsExtendedEntity $event */
        $event = reset($items);

        return $event->getEntity();
    }
}
