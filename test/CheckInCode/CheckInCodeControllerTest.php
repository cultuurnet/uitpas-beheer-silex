<?php

namespace CultuurNet\UiTPASBeheer\CheckInCode;

use Guzzle\Http\EntityBody;
use ValueObjects\StringLiteral\StringLiteral;

final class CheckInCodeControllerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CheckInCodeServiceInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $service;

    /**
     * @var CheckInCodeController
     */
    private $controller;

    public function setUp()
    {
        $this->service = $this->getMock(CheckInCodeServiceInterface::class);
        $this->controller = new CheckInCodeController($this->service);
    }

    /**
     * @test
     */
    public function it_responds_a_stream_when_downloading_as_zip()
    {
        $id = new StringLiteral('8a01e635-cbf2-4879-bdee-aea5f3066627');
        $fileName = 'qr-spaarcode-ancienne-belgique';

        $filePath = __DIR__ . '/data/UITPAS_QR_Ancienne_Belgique.zip';

        $contentType = 'application/x-zip-compressed';
        $originalContentDisposition = 'attachment; filename="UITPAS_QR_Ancienne_Belgique.zip"';

        $download = new CheckInCodeDownload(
            EntityBody::factory(
                fopen($filePath, 'r')
            ),
            new StringLiteral($contentType),
            new StringLiteral($originalContentDisposition)
        );

        $this->service->expects($this->once())
            ->method('download')
            ->with($id)
            ->willReturn($download);

        $response = $this->controller->downloadZip($id->toNative(), $fileName);

        $this->assertEquals(
            $contentType,
            $response->headers->get('Content-Type')
        );

        $this->assertEquals(
            'attachment; filename="qr-spaarcode-ancienne-belgique.zip"',
            $response->headers->get('Content-Disposition')
        );

        ob_start();
        $response->sendContent();
        $content = ob_get_contents();
        ob_end_clean();

        $this->assertEquals(
            file_get_contents($filePath),
            $content
        );
    }

    /**
     * @test
     */
    public function it_responds_a_stream_when_downloading_as_pdf()
    {
        $id = new StringLiteral('8a01e635-cbf2-4879-bdee-aea5f3066627');
        $fileName = 'qr-spaarcode-ancienne-belgique';

        $filePath = __DIR__ . '/data/UITPAS_QR_Ancienne_Belgique.pdf';

        $contentType = 'application/pdf';
        $originalContentDisposition = 'attachment; filename="UITPAS_QR_Ancienne_Belgique.pdf"';

        $download = new CheckInCodeDownload(
            EntityBody::factory(
                fopen($filePath, 'r')
            ),
            new StringLiteral($contentType),
            new StringLiteral($originalContentDisposition)
        );

        $this->service->expects($this->once())
            ->method('download')
            ->with($id)
            ->willReturn($download);

        $response = $this->controller->downloadPdf($id->toNative(), $fileName);

        $this->assertEquals(
            $contentType,
            $response->headers->get('Content-Type')
        );

        $this->assertEquals(
            'attachment; filename="qr-spaarcode-ancienne-belgique.pdf"',
            $response->headers->get('Content-Disposition')
        );

        ob_start();
        $response->sendContent();
        $content = ob_get_contents();
        ob_end_clean();

        $this->assertEquals(
            file_get_contents($filePath),
            $content
        );
    }
}
