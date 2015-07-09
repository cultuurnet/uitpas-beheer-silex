<?php

namespace CultuurNet\UiTPASBeheer\Activity;

use CultuurNet\UiTPASBeheer\Counter\CounterAwareUitpasService;
use ValueObjects\Number\Integer;
use ValueObjects\StringLiteral\StringLiteral;

class ActivityService extends CounterAwareUitpasService implements ActivityServiceInterface
{
    /**
     * @inheritdoc
     */
    public function search(DateType $date_type, Integer $limit, StringLiteral $query = null, Integer $page = null)
    {
        $activities = array();

        $searchOptions = new \CultureFeed_Uitpas_Event_Query_SearchEventsOptions();
        $searchOptions->sort = 'permanent desc,availableto asc';
        $result = $this->getUitpasService()->searchEvents($searchOptions);
        foreach ($result->objects as $object) {
            $activities[] = Activity::fromCultureFeedUitpasEvent($object);
        }

        return $activities;
    }
}
