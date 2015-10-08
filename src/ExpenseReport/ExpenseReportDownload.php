<?php

namespace CultuurNet\UiTPASBeheer\ExpenseReport;

use Guzzle\Stream\StreamInterface;
use ValueObjects\StringLiteral\StringLiteral;

final class ExpenseReportDownload
{
    /**
     * @var StreamInterface
     */
    private $stream;

    /**
     * @var StringLiteral
     */
    private $contentTypeHeader;

    /**
     * @var StringLiteral
     */
    private $contentDispositionHeader;

    /**
     * @param StreamInterface $stream
     * @param StringLiteral $contentTypeHeader
     * @param StringLiteral $contentDispositionHeader
     */
    public function __construct(
        StreamInterface $stream,
        StringLiteral $contentTypeHeader,
        StringLiteral $contentDispositionHeader
    ) {
        $this->stream = $stream;
        $this->contentTypeHeader = $contentTypeHeader;
        $this->contentDispositionHeader = $contentDispositionHeader;
    }

    /**
     * @return StreamInterface
     */
    public function getStream()
    {
        return $this->stream;
    }

    /**
     * @return array
     */
    public function getHeaders()
    {
        return [
            'Content-Type' => $this->contentTypeHeader->toNative(),
            'Content-Disposition' => $this->contentDispositionHeader->toNative(),
        ];
    }
}
