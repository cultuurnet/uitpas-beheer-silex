<?php
/**
 * @file
 */

namespace CultuurNet\UiTPASBeheer\Activity\SearchAPI2;

use CultuurNet\CalendarSummary\CalendarFormatterInterface;
use CultuurNet\Search\ServiceInterface;
use CultuurNet\UiTPASBeheer\Activity\Activity;
use CultuurNet\UiTPASBeheer\Activity\ActivityServiceInterface;
use CultuurNet\UiTPASBeheer\Activity\PagedResultSet;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASNumber;
use CultuurNet\UiTPASBeheer\Activity\Cdbid;

/**
 * Augments the available activity data from another ActivityServiceInterface
 * implementation with data coming from Search API v2.
 *
 * This decorator is designed with a potential failure of Search API v2 in mind.
 * In case of any failure during augmentation, the original activity data will
 * be used instead.
 */
class SearchAPI2AugmentedActivityService implements ActivityServiceInterface
{
    /**
     * @var ActivityServiceInterface
     */
    protected $activityService;

    /**
     * @var ServiceInterface
     */
    protected $searchService;

    /**
     * @var CalendarFormatterInterface
     */
    protected $calendarFormatter;

    /**
     * @param ActivityServiceInterface $activityService
     * @param ServiceInterface $searchService
     * @param CalendarFormatterInterface $calendarFormatter
     */
    public function __construct(
        ActivityServiceInterface $activityService,
        ServiceInterface $searchService,
        CalendarFormatterInterface $calendarFormatter
    ) {
        $this->activityService = $activityService;
        $this->searchService = $searchService;
        $this->calendarFormatter = $calendarFormatter;
    }

    /**
     * @param mixed $query
     *
     * @return PagedResultSet
     */
    public function search($query)
    {
        $resultSet = $this->activityService->search($query);

        $activities = $resultSet->getResults();

        $augmentedResultSet = new PagedResultSet(
            $resultSet->getTotal(),
            $activities
        );

        return $augmentedResultSet;
    }

    /**
     * @param Cdbid $eventCdbid
     * @param UiTPASNumber $uitpasNumber
     * @return Activity
     */
    public function get(UiTPASNumber $uitpasNumber, Cdbid $eventCdbid)
    {
        $activity = $this->activityService->get($uitpasNumber, $eventCdbid);

        return $activity;
    }

}
