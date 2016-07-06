<?php
/**
 * @file
 * Service to do balie insights requests.
 */

namespace CultuurNet\UiTPASBeheer\BalieInsights;

use Guzzle\Http\ClientInterface;
use Guzzle\Http\Exception\BadResponseException;
use Symfony\Component\HttpFoundation\ParameterBag;

class BalieInsightsService implements BalieInsightsServiceInterface
{
    /**
     * @var ClientInterface
     */
    protected $guzzleClient;

    /**
     * BalieInsightsService constructor.
     */
    public function __construct(
        ClientInterface $guzzleClient
    ) {
        $this->guzzleClient = $guzzleClient;
    }

    /**
     * @inheritdoc
     */
    public function getCardSales(ParameterBag $query)
    {
        return $this->request('GET', 'cardsales', $query);
    }

    public function getExchanges(ParameterBag $query)
    {
        return $this->request('GET', 'exchanges', $query);
    }

    public function getMias(ParameterBag $query)
    {
        return $this->request('GET', 'mias', $query);
    }

    public function getCheckins(ParameterBag $query)
    {
        return $this->request('GET', 'checkins', $query);
    }

    /**
     * Send and handle a request.
     */
    private function request($method, $uri, ParameterBag $query = null)
    {

        try {
            $queryParams = $query ? $query->all() : [];
            $response = $this->guzzleClient->createRequest($method, $uri, [], null, ['query' => $queryParams])->send()->getBody();
        } catch (BadResponseException $e) {
            $response = $e->getResponse()->getBody();
        }

        return $response;
    }
}
