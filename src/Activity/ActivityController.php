<?php

namespace CultuurNet\UiTPASBeheer\Activity;

use CultuurNet\Hydra\PagedCollection;
use CultuurNet\Hydra\Symfony\PageUrlGenerator;
use CultuurNet\UiTPASBeheer\Exception\UnknownParameterException;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASNumber;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
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

    /**
     * @var UrlGeneratorInterface
     */
    protected $urlGenerator;

    public function __construct(
        ActivityServiceInterface $activityService,
        QueryInterface $queryBuilder,
        UrlGeneratorInterface $urlGenerator
    ) {
        $this->activityService = $activityService;
        $this->queryBuilder = $queryBuilder;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     *
     * @throws DateTypeInvalidException
     *   When an invalid date type was provided.
     * @throws UnknownParameterException
     *   When an unknown parameter was provided.
     */
    public function search(Request $request)
    {
        $searchActivities = $this->queryBuilder;

        foreach ($request->query->all() as $parameter => $value) {
            switch ($parameter) {
                case 'date_type':
                    try {
                        $searchActivities = $searchActivities->withDateType(
                            DateType::fromNative($value)
                        );
                    } catch (\InvalidArgumentException $e) {
                        throw new DateTypeInvalidException($value);
                    };
                    break;

                case 'query':
                    $searchActivities = $searchActivities->withQuery(
                        new StringLiteral($value)
                    );
                    break;

                case 'uitpas_number':
                    $searchActivities = $searchActivities->withUiTPASNumber(
                        new UiTPASNumber($value)
                    );
                    break;

                case 'page':
                case 'limit':
                    // These are valid but we ignore them for now, we need them
                    // both in 1 method call.
                    break;

                default:
                    throw new UnknownParameterException($parameter);

            }
        }

        // Handle both page and limit parameters together.
        $page = $request->query->getInt('page', 1);
        $limit = $request->query->getInt('limit', 5);
        $searchActivities = $searchActivities->withPagination(
            new Integer($page),
            new Integer($limit)
        );

        $activityResultSet = $this->activityService->search($searchActivities);

        $pageUrlGenerator = new PageUrlGenerator(
            $request->query,
            $this->urlGenerator,
            $request->attributes->get('_route')
        );

        $pagedCollection = new PagedCollection(
            $page,
            $limit,
            $activityResultSet->getResults(),
            $activityResultSet->getTotal()->toNative(),
            $pageUrlGenerator
        );

        return JsonResponse::create($pagedCollection)
          ->setPrivate();
    }
}
