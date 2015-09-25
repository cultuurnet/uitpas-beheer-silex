<?php
/**
 * @file
 */

namespace CultuurNet\UiTPASBeheer\CheckInDevice;

use ValueObjects\StringLiteral\StringLiteral;

class CheckInDevice implements \JsonSerializable
{
    /**
     * @var StringLiteral
     */
    private $id;

    /**
     * @var StringLiteral
     */
    private $name;

    /**
     * @var StringLiteral
     */
    private $activityId;

    /**
     * @param StringLiteral $id
     * @param StringLiteral $name
     */
    public function __construct(StringLiteral $id, StringLiteral $name)
    {
        $this->id = $id;
        $this->name = $name;
    }

    /**
     * @param StringLiteral $activityId
     * @return CheckInDevice
     */
    public function withActivity(StringLiteral $activityId)
    {
        $c = clone $this;
        $c->activityId = $activityId;
        return $c;
    }

    /**
     * @param \CultureFeed_Uitpas_Counter_Device $cfDevice
     * @return CheckInDevice
     */
    public static function createFromCultureFeedCounterDevice(
        \CultureFeed_Uitpas_Counter_Device $cfDevice
    ) {
        $device = new self(
            new StringLiteral($cfDevice->consumerKey),
            new StringLiteral($cfDevice->name)
        );

        if ($cfDevice->cdbid) {
            $device = $device->withActivity(
                new StringLiteral($cfDevice->cdbid)
            );
        }

        return $device;
    }

    /**
     * @inheritdoc
     */
    public function jsonSerialize()
    {
        $json = [
          'id' => $this->id->toNative(),
          'name' => $this->name->toNative(),
        ];

        if ($this->activityId) {
            $json['activityId'] = $this->activityId->toNative();
        }

        return $json;
    }
}
