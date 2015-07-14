<?php

namespace CultuurNet\UiTPASBeheer\Activity;

use CultureFeed_Uitpas;
use CultureFeed_Uitpas_Event_Query_SearchEventsOptions;
use CultuurNet\Search\Guzzle\Service;
use CultuurNet\Search\Parameter\BooleanParameter;
use CultuurNet\Search\Parameter\Group;
use CultuurNet\Search\Parameter\Query;
use CultuurNet\UiTPASBeheer\Counter\CounterConsumerKey;
use ValueObjects\Number\Integer;
use ValueObjects\StringLiteral\StringLiteral;

class ActivityService implements ActivityServiceInterface
{
    /**
     * @var \CultureFeed_Uitpas
     */
    private $uitpasService;

    /**
     * @var CounterConsumerKey
     */
    private $counterConsumerKey;

    /**
     * @var CounterConsumerKey
     */
    private $searchService;

    /**
     * @param CultureFeed_Uitpas $uitpasService
     * @param CounterConsumerKey $counterConsumerKey
     * @param Service $searchService
     */
    public function __construct(
        CultureFeed_Uitpas $uitpasService,
        CounterConsumerKey $counterConsumerKey,
        Service $searchService
    ) {
        $this->uitpasService = $uitpasService;
        $this->counterConsumerKey = $counterConsumerKey;
        $this->searchService = $searchService;
    }

    /**
     * @inheritdoc
     */
    public function search(DateType $date_type, Integer $limit, StringLiteral $query = null, Integer $page = null)
    {
        $activities = array();

        $searchOptions = new CultureFeed_Uitpas_Event_Query_SearchEventsOptions();
        $searchOptions->sort = 'permanent desc,availableto asc';
        $result = $this->uitpasService->searchEvents($searchOptions);
        foreach ($result->objects as $object) {
            // Get the culturefeed event.
            $parameters = array();
            $parameters[] = new BooleanParameter('past', true);
            $parameters[] = new Group();
            $parameters[] = new Query('cdbid:' . $object->cdbid);
            $result = $this->searchService->search($parameters)->getItems();
            /* @var \CultuurNet\Search\ActivityStatsExtendedEntity $event */
            $event = reset($result);
            /* @var \CultureFeed_Cdb_Item_Event $cdbItem */
            $cdbItem = null;
            if ($event) {
                $cdbItem = $event->getEntity();
            }

            $activities[] = Activity::fromCultureFeedUitpasAndCdbEvent($object, $cdbItem);
        }

        return $activities;
    }
}
