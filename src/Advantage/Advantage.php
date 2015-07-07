<?php

namespace CultuurNet\UiTPASBeheer\Advantage;

use ValueObjects\Number\Integer;
use ValueObjects\StringLiteral\StringLiteral;

abstract class Advantage implements \JsonSerializable
{
    /**
     * @var AdvantageType
     */
    protected $type;

    /**
     * @var StringLiteral
     */
    protected $id;

    /**
     * @var StringLiteral
     */
    protected $title;

    /**
     * @var \ValueObjects\Number\Integer
     */
    protected $points;

    /**
     * @var bool
     */
    protected $exchangeable;

    /**
     * @var AdvantageIdentifier
     */
    protected $identifier;

    /**
     * @param AdvantageType $type
     * @param StringLiteral $id
     * @param StringLiteral $title
     * @param \ValueObjects\Number\Integer $points
     * @param bool $exchangeable
     */
    public function __construct(
        AdvantageType $type,
        StringLiteral $id,
        StringLiteral $title,
        Integer $points,
        $exchangeable
    ) {
        $this->type = $type;
        $this->id = $id;
        $this->title = $title;
        $this->points = $points;
        $this->exchangeable = (bool) $exchangeable;

        $this->identifier = AdvantageIdentifier::fromAdvantageTypeAndId(
            $this->type,
            $this->id
        );
    }

    /**
     * @return AdvantageType
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return StringLiteral
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return StringLiteral
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return \ValueObjects\Number\Integer
     */
    public function getPoints()
    {
        return $this->points;
    }

    /**
     * @return bool
     */
    public function isExchangeable()
    {
        return $this->exchangeable;
    }

    /**
     * @return AdvantageIdentifier
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            'id' => $this->identifier->toNative(),
            'title' => $this->title->toNative(),
            'points' => $this->points->toNative(),
            'exchangeable' => $this->exchangeable,
        ];
    }
}
