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
     * @param $activityService
     */
    protected $activityService;

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
     * @param string $uitpasNumber
     * @return JsonResponse
     *
     * @throws DateTypeInvalidException
     *   When an invalid date type was provided.
     * @throws UnknownParameterException
     *   When an unknown parameter was provided.
     */
    public function search(Request $request, $uitpasNumber)
    {
        $uitpasNumber = new UiTPASNumber($uitpasNumber);

        $searchActivities = $this->queryBuilder
            ->withUiTPASNumber($uitpasNumber);

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

        // From the UrlGeneratorInterface documentation:
        // Parameters that reference placeholders in the route pattern will substitute them in the
        // path or host. Extra params are added as query string to the URL.
        $pageUrlParameters = $request->query;
        $pageUrlParameters->set('uitpasNumber', $uitpasNumber->toNative());

        $pageUrlGenerator = new PageUrlGenerator(
            $pageUrlParameters,
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
