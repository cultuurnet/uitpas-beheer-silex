<?php

namespace CultuurNet\UiTPASBeheer\Activity;

use CultuurNet\UiTPASBeheer\Exception\ReadableCodeResponseException;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASNumber;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class CheckinController
{
    /**
     * @param checkinServiceInterface $checkinService
     */
    protected $checkinService;


    public function __construct(
        CheckinServiceInterface $checkinService
    ) {
        $this->checkinService = $checkinService;
    }

    /**
     * @param Request $request
     * @param string $uitpasNumber
     *
     * @return JsonResponse
     *
     * @throws ReadableCodeResponseException
     */
    public function checkin(Request $request, $uitpasNumber)
    {
        $uitpasNumber = new UiTPASNumber($uitpasNumber);
        $eventCdbid = $request->request->get('event_cdbid');

        $this->checkinService->checkin($uitpasNumber, $eventCdbid);
        try {
            $points = $this->checkinService->checkin($uitpasNumber, $eventCdbid);
        } catch (\CultureFeed_Exception $exception) {
            throw ReadableCodeResponseException::fromCultureFeedException($exception);
        }

        return JsonResponse::create()
            ->setData($points)
            ->setPrivate();
    }
}
