<?php

namespace CultuurNet\UiTPASBeheer\Activity;

use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASNumber;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use ValueObjects\Number\Integer;
use ValueObjects\StringLiteral\StringLiteral;

class ActivityController
{
    /**
     * @param ActivityServiceInterface $activityService
     */
    protected $passHolderService;

    /**
     * @var QueryInterface
     */
    protected $queryBuilder;

    public function __construct(
        ActivityServiceInterface $activityService,
        QueryInterface $queryBuilder
    ) {
        $this->activityService = $activityService;
        $this->queryBuilder = $queryBuilder;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws DateTypeInvalidException
     */
    public function search(Request $request)
    {
        $searchActivities = $this->queryBuilder;

        $dateType = $request->query->get('date_type');
        if ($dateType) {
            try {
                $searchActivities = $searchActivities->withDateType(
                    DateType::fromNative($dateType)
                );
            } catch (\InvalidArgumentException $e) {
                throw new DateTypeInvalidException($dateType);
            }
        }

        $page = $request->query->getInt('page', 1);
        $limit = $request->query->getInt('limit', 5);
        $searchActivities = $searchActivities->withPagination(
            new Integer($page),
            new Integer($limit)
        );

        $query = $request->query->get('query');
        if ($query) {
            $searchActivities = $searchActivities->withQuery(
                new StringLiteral($query)
            );
        }

        $uiTPASNumber = $request->query->get('uitpas_number');
        if ($uiTPASNumber) {
            $searchActivities = $searchActivities->withUiTPASNumber(
                new UiTPASNumber($uiTPASNumber)
            );
        }

        $events = $this->activityService->search($searchActivities);

        return JsonResponse::create()
          ->setData($events)
          ->setPrivate();
    }
}
