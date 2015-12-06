<?php

namespace CultuurNet\UiTPASBeheer\PointsTransaction;

use ValueObjects\DateTime\Date;
use ValueObjects\Number\Integer;
use ValueObjects\StringLiteral\StringLiteral;

class PointsTransaction implements \JsonSerializable
{
    /**
     * @var PointsTransactionType
     */
    protected $type;

    /**
     * @var Date
     */
    protected $creationDate;

    /**
     * @var StringLiteral
     */
    protected $title;

    /**
     * @var \ValueObjects\Number\Integer
     */
    protected $points;

    /**
     * PointsTransaction constructor.
     */
    public function __construct(
        PointsTransactionType $type,
        Date $creationDate,
        StringLiteral $title,
        Integer $points
    ) {
        $this->type = $type;
        $this->creationDate = $creationDate;
        $this->title = $title;
        $this->points = $points;
    }

    /**
     * @return PointsTransactionType
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return Date
     */
    public function getCreationDate()
    {
        return $this->creationDate;
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
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize()
    {
        return [
            'creation_date' => $this->creationDate->toNativeDateTime()->format('Y-m-d'),
            'title' => $this->title->toNative(),
            'points' => $this->points->toNative(),
        ];
    }
}
