<?php

namespace CultuurNet\UiTPASBeheer\Advantage;

use CultuurNet\UiTPASBeheer\Advantage\CashIn\CashInContentType;

abstract class Advantage implements \JsonSerializable
{
    /**
     * @var AdvantageType
     */
    protected $type;

    /**
     * @var string
     */
    protected $id;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var int
     */
    protected $points;

    /**
     * @param AdvantageType $type
     * @param string $id
     * @param string $title
     * @param int $points
     */
    public function __construct(AdvantageType $type, $id, $title, $points)
    {
        $this->type = $type;
        $this->id = (string) $id;
        $this->title = (string) $title;
        $this->points = (int) $points;
    }

    /**
     * @return AdvantageType
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return int
     */
    public function getPoints()
    {
        return $this->points;
    }

    /**
     * @return array
     */
    function jsonSerialize()
    {
        $type = $this->getType()->toNative();
        $cashInContentType = CashInContentType::fromAdvantageType($this->getType())->toNative();

        return [
            'id' => $this->getId(),
            'title' => $this->getTitle(),
            'points' => $this->getPoints(),
            'type' => $type,
            'cashInContentType' => $cashInContentType,
        ];
    }
}
