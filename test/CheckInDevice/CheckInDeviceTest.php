<?php
/**
 * @file
 */

namespace CultuurNet\UiTPASBeheer\CheckInDevice;

use ValueObjects\StringLiteral\StringLiteral;

class CheckInDeviceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_can_be_factored_from_a_culturefeed_counter_device()
    {
        $cfDevice = new \CultureFeed_Uitpas_Counter_Device();
        $cfDevice->consumerKey = 'foo';
        $cfDevice->name = 'test device';
        $cfDevice->cdbid = '123-456-789';

        $expectedDevice = (new CheckInDevice(
            new StringLiteral('foo'),
            new StringLiteral('test device')
        ))->withActivity(new StringLiteral('123-456-789'));

        $device = CheckInDevice::createFromCultureFeedCounterDevice($cfDevice);

        $this->assertEquals(
            $expectedDevice,
            $device
        );
    }

    /**
     * @test
     */
    public function it_serializes_to_json()
    {
        $device = (new CheckInDevice(
            new StringLiteral('foo'),
            new StringLiteral('test device')
        ))->withActivity(new StringLiteral('123-456-789'));

        $json = json_encode($device);

        $this->assertJsonStringEqualsJsonFile(__DIR__ . '/data/device.json', $json);
    }
}
