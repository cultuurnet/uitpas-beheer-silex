<?php
/**
 * @file
 */

namespace CultuurNet\UiTPASBeheer\Activity\SearchAPI2;

use CultuurNet\CalendarSummary\CalendarFormatterInterface;
use CultuurNet\CalendarSummary\FormatterException;
use CultuurNet\Search\Parameter\BooleanParameter;
use CultuurNet\Search\Parameter\Group;
use CultuurNet\Search\Parameter\Query;
use CultuurNet\Search\ServiceInterface;
use CultuurNet\UiTPASBeheer\Activity\Activity;
use CultuurNet\UiTPASBeheer\Activity\ActivityServiceInterface;
use CultuurNet\UiTPASBeheer\Activity\PagedResultSet;
use CultuurNet\UiTPASBeheer\Properties\Location;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASNumber;
use ValueObjects\Number\Natural;
use ValueObjects\StringLiteral\StringLiteral;
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

        $originalActivities = $resultSet->getResults();
        $augmentedActivities = array_map(
            function (Activity $activity) {
                try {
                    return $this->augmentActivity($activity);
                } catch (\Exception $e) {
                    return $activity;
                }
            },
            $originalActivities
        );

        $augmentedResultSet = new PagedResultSet(
            $resultSet->getTotal(),
            $augmentedActivities
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

        try {
            $augmentedActivity = $this->augmentActivity($activity);
        } catch (\Exception $e) {
            $augmentedActivity = $activity;
        }

        return $augmentedActivity;
    }

    /**
     * @param Activity $activity
     * @return Activity
     */
    private function augmentActivity(Activity $activity)
    {
        $cdbEvent = $this->getCdbEvent($activity);
        $details = $cdbEvent->getDetails()->getDetailByLanguage('nl');

        // Add the activity description
        $description = new StringLiteral(
            trim((string) $details->getShortDescription())
        );
        $augmentedActivity = $activity->withDescription($description);

        // Try to add the activity time.
        $calendar = $cdbEvent->getCalendar();
        try {
            $when = new StringLiteral(
                (string) $this->calendarFormatter->format($calendar, 'md')
            );

            $augmentedActivity = $augmentedActivity->withWhen($when);
        } catch (FormatterException $e) {
            // Format not supported for the calendar type, for example for a
            // CultureFeed_Cdb_Data_Calendar_TimestampList.
        }

        // Try to add the activity location.
        $cdbLocation = $cdbEvent->getLocation();
        try {
            $location = Location::fromCultureFeedCbdDataLocation($cdbLocation);

            $augmentedActivity = $augmentedActivity->withLocation($location);
        } catch (FormatterException $e) {

        }

        // Add the activity age target.
        if (!is_null($cdbEvent->getAgeFrom())) {
            $augmentedActivity = $augmentedActivity->withAge(
                new Natural($cdbEvent->getAgeFrom())
            );
        }

        return $augmentedActivity;
    }

    /**
     * @param Activity $activity
     *
     * @return \CultureFeed_Cdb_Item_Event
     */
    private function getCdbEvent(Activity $activity)
    {
        $parameters = [
            new BooleanParameter('past', true),
            new BooleanParameter('unavailable', true),
            new Group(),
            new Query('cdbid:"' . $activity->getId()->toNative() . '"'),
        ];

        $result = $this->searchService->search($parameters);

        if (1 !== $result->getCurrentCount()) {
            throw new \RuntimeException(
                'Expected exactly 1 event to be returned, received ' . $result->getCurrentCount() . ' results'
            );
        }

        $items = $result->getItems();

        /* @var \CultuurNet\Search\ActivityStatsExtendedEntity $event */
        $event = reset($items);

        return $event->getEntity();
    }
}
