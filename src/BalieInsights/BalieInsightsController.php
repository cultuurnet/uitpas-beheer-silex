<?php
/**
 * @file
 * Controller for balie insights.
 */

namespace CultuurNet\UiTPASBeheer\BalieInsights;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class BalieInsightsController
{
    /**
     * @var BalieInsightsServiceInterface
     */
    private $balieInsightsService;

    public function __construct(BalieInsightsServiceInterface $balieInsightsService)
    {
        $this->balieInsightsService = $balieInsightsService;
    }

    /**
     * @return JsonResponse
     */
    public function getCardSales(Request $request)
    {
        return $this->balieInsightsService->getCardSales($request->query);
    }

    /**
     * @return JsonResponse
     */
    public function getExchanges(Request $request)
    {
        return $this->balieInsightsService->getExchanges($request->query);
    }

    /**
     * @return JsonResponse
     */
    public function getMias(Request $request)
    {
        return $this->balieInsightsService->getMias($request->query);
    }

    /**
     * @return JsonResponse
     */
    public function getCheckins(Request $request)
    {
        return $this->balieInsightsService->getCheckins($request->query);
    }
}
