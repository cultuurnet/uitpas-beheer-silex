<?php

namespace CultuurNet\UiTPASBeheer\CheckInCode;

use CultuurNet\Auth\ConsumerCredentials;
use CultuurNet\Auth\Guzzle\OAuthProtectedService;
use CultuurNet\Auth\TokenCredentials;
use CultuurNet\UiTPASBeheer\Counter\CounterConsumerKey;
use Guzzle\Http\Exception\ClientErrorResponseException;
use ValueObjects\StringLiteral\StringLiteral;

final class CheckInCodeService extends OAuthProtectedService implements CheckInCodeServiceInterface
{
    /**
     * @var CounterConsumerKey
     */
    private $counterConsumerKey;

    /**
     * @param CounterConsumerKey $counterConsumerKey
     * @param string $baseUrl
     * @param ConsumerCredentials $consumerCredentials
     * @param TokenCredentials|null $tokenCredentials
     */
    public function __construct(
        CounterConsumerKey $counterConsumerKey,
        $baseUrl,
        ConsumerCredentials $consumerCredentials,
        TokenCredentials $tokenCredentials = null
    ) {
        parent::__construct($baseUrl, $consumerCredentials, $tokenCredentials);
        $this->counterConsumerKey = $counterConsumerKey;
    }

    /**
     * @param StringLiteral $activityId
     * @param bool $zipped
     * @return CheckInCodeDownload
     */
    public function download(StringLiteral $activityId, $zipped = false)
    {
        // We're not using CultureFeed-PHP here because we need to get certain headers from the response.
        $client = $this->getClient();

        // Note that the `zipped` parameter has to be a string, otherwise Guzzle will calculate an incorrect OAuth
        // signature for the request because it omits `false` values while the UiTPAS server doesn't.
        $request = $client->post(
            'uitpas/checkincode/pdf/' . $activityId->toNative(),
            null,
            [
                'balieConsumerKey' => $this->counterConsumerKey->toNative(),
                'zipped' => $zipped ? 'true' : 'false',
            ]
        );

        try {
            $response = $request->send();
        } catch (ClientErrorResponseException $e) {
            $response = $e->getResponse();
            $cfResponse = \CultureFeed_Response::createFromResponseBody($response->getBody(true));

            switch ($cfResponse->getCode()) {
                case 'CHECKINCODE_NO_FUTURE_CHECKIN_PERIODS':
                    throw new NoFurtherCheckInPeriodsException();
                    break;

                case 'UNKNOWN_EVENT_CDBID':
                    throw new UnknownActivityException();
                    break;

                default:
                    throw $e;
                    break;
            }
        }

        $contentType = $response->getHeader('Content-Type');
        $contentDisposition = $response->getHeader('Content-Disposition');

        $contentStream = $response->getBody(false);

        return new CheckInCodeDownload(
            $contentStream,
            new StringLiteral((string) $contentType),
            new StringLiteral((string) $contentDisposition)
        );
    }
}
