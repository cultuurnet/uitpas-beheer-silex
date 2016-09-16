<?php
/**
 * @file
 * Service to do balie insights requests.
 */

namespace CultuurNet\UiTPASBeheer\BalieInsights;

use Guzzle\Http\ClientInterface;
use Guzzle\Http\Exception\BadResponseException;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Response;

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
            $response = $this->guzzleClient->createRequest($method, $uri, [], null, ['query' => $queryParams])->send();
        } catch (BadResponseException $e) {
            $response = $e->getResponse();
        }

        $silexResponse = new Response($response->getBody()->__toString(), $response->getStatusCode(), $response->getHeaders()->toArray());
        $silexResponse->headers->remove('Transfer-Encoding');

        return $silexResponse;
    }
}
