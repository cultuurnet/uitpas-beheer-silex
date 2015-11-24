<?php

namespace CultuurNet\UiTPASBeheer\Advantage;

use CultuurNet\UiTPASBeheer\Properties\CityCollection;
use ValueObjects\DateTime\Date;
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
     * @var StringLiteral|null
     */
    protected $description1;

    /**
     * @var StringLiteral|null
     */
    protected $description2;

    /**
     * @var CityCollection|null
     */
    protected $validForCities;

    /**
     * @var \CultureFeed_Uitpas_Counter_Employee[]|null
     */
    protected $validForCounters;

    /**
     * @var Date|null
     */
    protected $endDate;

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

        $this->validForCounters = array();
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
     * @return StringLiteral|null
     */
    public function getDescription1()
    {
        return $this->description1;
    }

    /**
     * @return StringLiteral|null
     */
    public function getDescription2()
    {
        return $this->description2;
    }

    /**
     * @return CityCollection|null
     */
    public function getValidForCities()
    {
        return $this->validForCities;
    }

    /**
     * @return \CultureFeed_Uitpas_Counter_Employee[]
     */
    public function getValidForCounters()
    {
        return $this->validForCounters;
    }

    /**
     * @return Date|null
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        $data = [
            'id' => $this->identifier->toNative(),
            'title' => $this->title->toNative(),
            'points' => $this->points->toNative(),
            'exchangeable' => $this->exchangeable,
        ];

        if (!is_null($this->description1)) {
            $data['description1'] = $this->getDescription1()->toNative();
        }

        if (!is_null($this->description2)) {
            $data['description2'] = $this->getDescription2()->toNative();
        }

        if (!is_null($this->validForCities)) {
            $data['validForCities'] = array_values($this->getValidForCities()->jsonSerialize());
        }

        if (!is_null($this->validForCounters)) {
            $data['validForCounters'] = $this->getValidForCounters();
        }

        if (!is_null($this->endDate)) {
            $data['endDate'] = $this->getEndDate()
                ->toNativeDateTime()
                ->format('Y-m-d');
        }

        return $data;
    }

    /**
     * @param StringLiteral $description1
     * @return Advantage
     */
    public function withDescription1(StringLiteral $description1)
    {
        $c = clone $this;
        $c->description1 = $description1;
        return $c;
    }

    /**
     * @param StringLiteral $description2
     * @return Advantage
     */
    public function withDescription2(StringLiteral $description2)
    {
        $c = clone $this;
        $c->description2 = $description2;
        return $c;
    }

    /**
     * @param CityCollection $validForCities
     * @return Advantage
     */
    public function withValidForCities(CityCollection $validForCities)
    {
        $c = clone $this;
        $c->validForCities = $validForCities;
        return $c;
    }

    /**
     * @param array $validForCounters
     * @return Advantage
     */
    public function withValidForCounters(array $validForCounters)
    {
        $c = clone $this;
        $c->validForCounters = $validForCounters;
        return $c;
    }

    /**
     * @param Date $endDate
     * @return Advantage
     */
    public function withEndDate(Date $endDate)
    {
        $c = clone $this;
        $c->endDate = $endDate;
        return $c;
    }
}
