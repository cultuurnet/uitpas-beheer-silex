<?php

namespace CultuurNet\UiTPASBeheer\Coupon;

use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASNumber;
use ValueObjects\StringLiteral\StringLiteral;

class CouponControllerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CouponController
     */
    protected $controller;

    /**
     * @var CouponServiceInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $service;

    public function setUp()
    {
        $this->service = $this->getMock(CouponServiceInterface::class);
        $this->controller = new CouponController($this->service);
    }

    /**
     * @param string $filePath
     * @return string
     */
    private function getJson($filePath)
    {
        return file_get_contents(__DIR__ . '/' . $filePath);
    }

    /**
     * @param string $json
     *   Actual JSON string.
     * @param string $filePath
     *   Path to the file with the expected JSON.
     */
    private function assertJsonEquals($json, $filePath)
    {
        $expected = $this->getJson($filePath);
        $expected = json_decode($expected);

        $actual = json_decode($json);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function it_responds_the_coupons_for_passholder()
    {

        $uitpasNumber = new UiTPASNumber('0930000125607');

        $coupons = array();
        for ($i = 1; $i <= 3; $i++) {
            $id = new StringLiteral((string) $i);
            $name = new StringLiteral('Coupon ' . $i);
            $coupon = new Coupon($id, $name);
            $coupons[] = $coupon;
        }

        $this->service->expects($this->once())
            ->method('getCouponsForPassholder')
            ->with($uitpasNumber)
            ->willReturn($coupons);

        $response = $this->controller->getCouponsForPassholder($uitpasNumber->toNative());
        $content = $response->getContent();

        $this->assertJsonEquals($content, 'data/coupons.json');
    }
}
