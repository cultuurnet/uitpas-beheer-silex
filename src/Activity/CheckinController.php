<?php

namespace CultuurNet\UiTPASBeheer\Activity;

use CultuurNet\UiTPASBeheer\Exception\CompleteResponseException;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASNumber;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use CultuurNet\UiTPASBeheer\Activity\CheckinCommandDeserializer;
use CultuurNet\UiTPASBeheer\Activity\CheckinCommand;
use CultuurNet\Deserializer\DeserializerInterface;
use ValueObjects\StringLiteral\StringLiteral;

class CheckinController
{
    /**
     * @var CheckinServiceInterface
     */
    protected $checkinService;

    /**
     * @var ActivityServiceInterface
     */
    protected $activityService;

    /**
     * @var DeserializerInterface
     */
    protected $checkinCommandDeserializer;

    public function __construct(
        CheckinServiceInterface $checkinService,
        ActivityServiceInterface $activityService,
        DeserializerInterface $checkinCommandDeserializer
    ) {
        $this->checkinService = $checkinService;
        $this->activityService = $activityService;
        $this->checkinCommandDeserializer = $checkinCommandDeserializer;
    }

    /**
     * @param UiTPASNumber $uitpasNumber
     * @param Cdbid $eventCdbid
     * @return JsonResponse
     * @throws ActivityNotFoundException
     *   When no advantage was found for the specified advantage identifier.
     */
    protected function getActivityJsonResponse($uitpasNumber, Cdbid $eventCdbid)
    {
        $activity = $this->activityService->get(
            $uitpasNumber,
            $eventCdbid
        );

        if (is_null($activity)) {
            throw new ActivityNotFoundException($eventCdbid);
        }

        return JsonResponse::create()
            ->setData($activity)
            ->setPrivate(true);
    }

    /**
     * @param Request $request
     * @param string $uitpasNumber
     *
     * @return JsonResponse
     *
     * @throws CompleteResponseException
     */
    public function checkin(Request $request, $uitpasNumber)
    {
        $uitpasNumber = new UiTPASNumber($uitpasNumber);

        /**
         * @var CheckinCommand $checkinCommand
         */
        $checkinCommand = $this
            ->checkinCommandDeserializer
            ->deserialize(new StringLiteral($request->getContent()));

        $eventCdbid = $checkinCommand->getEventCdbid();

        try {
            $this->checkinService->checkin($uitpasNumber, $eventCdbid);
        } catch (\CultureFeed_Exception $exception) {
            throw CompleteResponseException::fromCultureFeedException($exception);
        }

        return $this->getActivityJsonResponse($uitpasNumber, $eventCdbid);
    }
}
