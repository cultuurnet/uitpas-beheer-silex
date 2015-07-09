<?php

namespace CultuurNet\UiTPASBeheer\Activity;

use CultuurNet\UiTPASBeheer\Exception\MissingParameterException;
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

    public function __construct(ActivityServiceInterface $activityService)
    {
        $this->activityService = $activityService;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws MissingParameterException
     * @throws DateTypeInvalidException
     */
    public function search(Request $request)
    {
        // Validate date type.
        $date_type = null;
        if (!$request->query->has('date_type')) {
            throw new MissingParameterException('date_type');
        }
        try {
            $date_type = DateType::fromNative($request->query->get('date_type'));
        } catch (\InvalidArgumentException $e) {
            throw new DateTypeInvalidException($request->query->get('date_type'));
        }

        // Validate limit.
        $limit = $request->query->get('limit');
        if (empty($limit)) {
            throw new MissingParameterException('limit');
        }
        $limit = new Integer((int) $limit);

        // Get query value or set default.
        $query = null;
        if ($request->query->has('query')) {
            $query = new StringLiteral((string) $request->query->get('query'));
        }

        // Get page value or set default.
        $page = null;
        if ($request->query->has('page')) {
            $page = new Integer((int) $request->query->get('page'));
        }

        // Get the events and return them.
        $events = $this->activityService->search($date_type, $limit, $query, $page);

        return JsonResponse::create()
          ->setData($events)
          ->setPrivate();
    }
}
