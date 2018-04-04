<?php

namespace CultuurNet\UiTPASBeheer\Activity;

use CultuurNet\Hydra\PagedCollection;
use CultuurNet\Hydra\Symfony\PageUrlGenerator;
use CultuurNet\UiTPASBeheer\Activity\CultureFeedUiTPAS\Query;
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
    public function search(Request $request, $uitpasNumber = null)
    {
        $searchActivities = clone $this->queryBuilder;

        if ($uitpasNumber) {
            $uitpasNumber = new UiTPASNumber($uitpasNumber);

            $searchActivities = $this->queryBuilder
                ->withUiTPASNumber($uitpasNumber);
        }

        foreach ($request->query->all() as $parameter => $value) {
            switch ($parameter) {
                case 'date_type':
                    try {
                        if ($value !== 'choose_date') {
                            $searchActivities = $searchActivities->withDateType(
                                DateType::fromNative($value)
                            );
                        }
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

                case 'startDate':
                case 'endDate':
                    // These are valid but we ignore them for now, we need them
                    // both in 1 method call.
                    break;

                default:
                    throw new UnknownParameterException($parameter);

            }
        }

        // Possibly add date range parameters.
        $startDate = $request->query->getInt('startDate');
        $endDate = $request->query->getInt('endDate');
        $searchActivities = $this->formatAndAddDateRangeParameters($searchActivities, $startDate, $endDate);

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

        if ($uitpasNumber) {
            $pageUrlParameters->set('uitpasNumber', $uitpasNumber->toNative());
        }

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

    /**
     * Helper function to add date range parameters in the correct format.
     * We also provide defaults for a missing start/end date.
     *
     * @param QueryInterface $searchActivities
     * @param int $startDate
     * @param int $endDate
     * @return QueryInterface|static
     */
    private function formatAndAddDateRangeParameters(QueryInterface $searchActivities, $startDate, $endDate)
    {
        if ($startDate || $endDate) {

            $startDate = $startDate ? date("Y-m-d\TH:i:s\Z", $startDate) : date("Y-m-d\TH:i:s\Z", strtotime('-10 years'));
            $endDate = $endDate ? date("Y-m-d\TH:i:s\Z", $endDate) : date("Y-m-d\TH:i:s\Z", strtotime('+10 years'));

            $searchActivities = $searchActivities->withDateRange(
                new StringLiteral($startDate),
                new StringLiteral($endDate)
            );
        }

        return $searchActivities;
    }
}
