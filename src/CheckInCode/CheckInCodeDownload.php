<?php

namespace CultuurNet\UiTPASBeheer\CheckInCode;

use CultuurNet\UiTPASBeheer\Http\ContentDispositionHeader;
use Guzzle\Stream\StreamInterface;
use ValueObjects\StringLiteral\StringLiteral;

final class CheckInCodeDownload
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
     * @var ContentDispositionHeader
     */
    private $contentDispositionHeader;

    /**
     * @param StreamInterface $stream
     * @param StringLiteral $contentTypeHeader
     * @param ContentDispositionHeader $contentDispositionHeader
     */
    public function __construct(
        StreamInterface $stream,
        StringLiteral $contentTypeHeader,
        ContentDispositionHeader $contentDispositionHeader
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
     * @param ContentDispositionHeader $contentDispositionHeader
     * @return self
     */
    public function withContentDispositionHeader(ContentDispositionHeader $contentDispositionHeader)
    {
        $c = clone $this;
        $c->contentDispositionHeader = $contentDispositionHeader;
        return $c;
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
