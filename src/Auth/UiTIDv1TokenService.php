<?php

declare(strict_types=1);

namespace CultuurNet\UiTPASBeheer\Auth;

use CultureFeed_SimpleXMLElement;
use CultuurNet\Auth\Guzzle\OAuthProtectedService;
use CultuurNet\Auth\TokenCredentials;
use CultuurNet\Auth\User;
use Exception;
use Guzzle\Http\Exception\BadResponseException;

final class UiTIDv1TokenService extends OAuthProtectedService
{
    private const CODE_FAILED = 'FAILED';

    /**
     * Exchanges an Auth0 token for a UiTID v1 token and secret, and the corresponding user id.
     * @see https://jira.uitdatabank.be/browse/UPS-3180#comment-105142
     * @throws UiTIDv1TokenException
     */
    public function getV1TokenForAuth0AccessToken(string $auth0AccessToken): User
    {
        // Get a HTTP client pre-configured to make requests to UiTID v1.
        $client = $this->getClient();

        // Prepare a POST request to the endpoint where we can exchange the Auth0 token.
        $request = $client->post('authapi/auth0at');
        $request->addPostFields(['at' => $auth0AccessToken]);

        try {
            $response = $request->send();
        } catch (BadResponseException $exception) {
            // If the response status code is not 200, try to throw an informative exception based on the known status
            // codes the endpoint can return.
            $response = $exception->getResponse();

            switch ($response->getStatusCode()) {
                case 401:
                    throw UiTIDv1TokenException::unauthorized();

                case 403:
                    throw UiTIDv1TokenException::noPermission();

                default:
                    throw UiTIDv1TokenException::unknown($response->getStatusCode(), $response->getReasonPhrase());
            }
        }

        $responseBody = $response->getBody(true);

        try {
            $xml = new CultureFeed_SimpleXMLElement($responseBody);
        } catch (Exception $e) {
            throw UiTIDv1TokenException::invalidXmlResponse('Could not parse XML.');
        }

        // The <response><code> tag will contain SUCCESS or FAILED.
        // If it's not found or contains FAILED, throw an exception.
        $code = $xml->xpath_str('/response/code');
        if ($code === null) {
            throw UiTIDv1TokenException::invalidXmlResponse('/response/code missing');
        }
        if ($code === self::CODE_FAILED) {
            $message = $code = $xml->xpath_str('/response/message');
            if ($message === null) {
                throw UiTIDv1TokenException::invalidXmlResponse('/response/message missing');
            }
            throw UiTIDv1TokenException::failed($message);
        }

        $token = $xml->xpath_str('/response/token/token');
        $secret = $xml->xpath_str('/response/token/tokenSecret');
        $userId = $xml->xpath_str('/response/token/user/uid');

        if ($token === null) {
            throw UiTIDv1TokenException::invalidXmlResponse('/response/token/token missing.');
        }
        if ($secret === null) {
            throw UiTIDv1TokenException::invalidXmlResponse('/response/token/tokenSecret missing.');
        }
        if ($userId === null) {
            throw UiTIDv1TokenException::invalidXmlResponse('/response/token/user/uid missing.');
        }

        return new User($userId, new TokenCredentials($token, $secret));
    }
}
