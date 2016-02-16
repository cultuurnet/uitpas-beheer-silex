<?php
/**
 * @file
 */

namespace CultuurNet\UiTPASBeheer\School;

use CultureFeed_Uitpas_Counter;
use JsonSerializable;
use ValueObjects\StringLiteral\StringLiteral;

final class School implements JsonSerializable
{
    /**
     * The unique ID.
     *
     * @var StringLiteral
     */
    private $id;

    /**
     * A human readable name.
     *
     * @var StringLiteral
     */
    private $name;

    public function __construct(StringLiteral $id, StringLiteral $name = null)
    {
        $this->id = $id;
        $this->name = $name;
    }

    /**
     * @return StringLiteral
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return StringLiteral|null
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param CultureFeed_Uitpas_Counter $counter
     * @return School
     */
    public static function fromCultureFeedCounter(
        CultureFeed_Uitpas_Counter $counter
    ) {
        return new School(
            new StringLiteral($counter->id),
            new StringLiteral($counter->name)
        );
    }

    /**
     * @inheritdoc
     */
    public function jsonSerialize()
    {
        $data = [
            'id' => $this->id->toNative(),
        ];

        if ($this->name) {
            $data['name'] = $this->name->toNative();
        }

        return $data;
    }
}
