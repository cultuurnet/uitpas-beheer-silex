<?php

namespace CultuurNet\UiTPASBeheer\Advantage;

use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASNumber;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGenerator;

class AdvantageController
{
    const WELCOME_ADVANTAGE_EXCHANGE_ROUTE_NAME = 'uitpas.advantage.welcome.exchange';
    const POINTS_PROMOTION_EXCHANGE_ROUTE_NAME = 'uitpas.advantage.points.exchange';

    /**
     * @var AdvantageService
     */
    protected $service;

    /**
     * @var UrlGenerator
     */
    protected $urlGenerator;

    /**
     * @param AdvantageService $service
     */
    public function __construct(AdvantageService $service, UrlGenerator $urlGenerator)
    {
        $this->service = $service;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * @return JsonResponse
     */
    public function getList(Request $request)
    {
        $uitpasNumber = $request->query->get('uitpasNumber');
        $uitpasNumber = new UiTPASNumber($uitpasNumber);

        $welcomeAdvantages = $this->service->getExchangeableWelcomeAdvantages($uitpasNumber);
        $welcomeAdvantages = $this->advantagesToJsonData(
            $welcomeAdvantages,
            self::WELCOME_ADVANTAGE_EXCHANGE_ROUTE_NAME
        );

        $pointPromotions = $this->service->getExchangeablePointPromotions($uitpasNumber);
        $pointPromotions = $this->advantagesToJsonData(
            $pointPromotions,
            self::POINTS_PROMOTION_EXCHANGE_ROUTE_NAME
        );

        $advantages = array_merge($pointPromotions, $welcomeAdvantages);

        return JsonResponse::create()
            ->setData($advantages)
            ->setPrivate(true);
    }

    /**
     * @param Advantage[] $advantages
     * @param string $exchangeRouteName
     * @return array
     */
    private function advantagesToJsonData($advantages, $exchangeRouteName)
    {
        $data = [];

        foreach ($advantages as $advantage) {
            $exchangeLink = $this->urlGenerator->generate(
                $exchangeRouteName,
                ['advantageId' => $advantage->getId()]
            );

            $data[] = [
                'id' => $advantage->getId(),
                'title' => $advantage->getTitle(),
                'points' => $advantage->getPoints(),
                'links' => [
                    [
                        'rel' => 'exchange',
                        'href' => $exchangeLink,
                    ],
                ]
            ];
        }

        return $data;
    }
}
