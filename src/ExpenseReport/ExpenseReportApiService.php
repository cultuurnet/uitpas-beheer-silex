<?php

namespace CultuurNet\UiTPASBeheer\ExpenseReport;

use CultuurNet\Auth\ConsumerCredentials;
use CultuurNet\Auth\Guzzle\OAuthProtectedService;
use CultuurNet\Auth\TokenCredentials;
use CultuurNet\UiTPASBeheer\Counter\CounterConsumerKey;
use CultuurNet\UiTPASBeheer\ExpenseReport\Properties\ExpenseReportId;
use Guzzle\Stream\PhpStreamRequestFactory;
use ValueObjects\StringLiteral\StringLiteral;

class ExpenseReportApiService extends OAuthProtectedService
{
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
     * @param ExpenseReportId $id
     * @return ExpenseReportDownload
     */
    public function download(ExpenseReportId $id)
    {
        // We're not using CultureFeed-PHP here because we need to get certain headers from the response.
        $client = $this->getClient();

        $request = $client->get('uitpas/report/financialoverview/organiser/' . $id->toNative() . '/download');
        $query = $request->getQuery();
        $query->add('balieConsumerKey', $this->counterConsumerKey->toNative());

        $response = $request->send();

        $contentType = $response->getHeader('Content-Type');
        $contentDisposition = $response->getHeader('Content-Disposition');

        // @see http://jira.uitdatabank.be:8080/browse/UBR-296
        $contentDisposition = $this->fixContentDispositionFileName($contentDisposition);

        $contentStream = $response->getBody(false);

        return new ExpenseReportDownload(
            $contentStream,
            new StringLiteral((string) $contentType),
            new StringLiteral((string) $contentDisposition)
        );
    }

    /**
     * Makes sure the filename in the content disposition header is encapsulated in quotation marks.
     * @see http://jira.uitdatabank.be:8080/browse/UBR-296
     *
     * @param string $contentDisposition
     *
     * @return string
     */
    private function fixContentDispositionFileName($contentDisposition)
    {
        $pattern = '/^attachment; filename="?(.*?)"?$/';
        $match = array();

        if (!preg_match($pattern, $contentDisposition, $match)) {
            return $contentDisposition;
        }

        return sprintf(
            'attachment; filename="%s"',
            $match[1]
        );
    }
}
