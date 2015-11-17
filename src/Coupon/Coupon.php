<?php

namespace CultuurNet\UiTPASBeheer\Coupon;

use CultuurNet\UiTPASBeheer\Properties\PeriodConstraint;
use ValueObjects\Number\Integer;
use ValueObjects\StringLiteral\StringLiteral;
use ValueObjects\DateTime\Date;

class Coupon implements \JsonSerializable
{
    /**
     * @var StringLiteral
     */
    protected $id;

    /**
     * @var StringLiteral
     */
    protected $name;

    /**
     * @var StringLiteral
     */
    protected $description;

    /**
     * @var Date
     */
    protected $startDate;

    /**
     * @var PeriodConstraint
     */
    protected $remainingTotal;

    /**
     * @var Date
     */
    protected $expirationDate;

    public function __construct(StringLiteral $id, stringLiteral $name)
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
     * @return StringLiteral
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return StringLiteral
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return Date
     */
    public function getExpirationDate()
    {
        return $this->expirationDate;
    }

    /**
     * @return Date
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * @return PeriodConstraint
     */
    public function getRemainingTotal()
    {
        return $this->remainingTotal;
    }

    /**
     * @param StringLiteral $description
     * @return Coupon
     */
    public function withDescription(StringLiteral $description)
    {
        return $this->with('description', $description);
    }

    /**
     * @param \ValueObjects\Number\Integer $expirationDate
     * @return Coupon
     */
    public function withExpirationDate(Integer $expirationDate)
    {
        $dateTime = new \DateTime('@' . $expirationDate);
        $date = Date::fromNativeDateTime($dateTime);

        return $this->with('expirationDate', $date);
    }

    /**
     * @param \ValueObjects\Number\Integer $startDate
     * @return Coupon
     */
    public function withStartDate(Integer $startDate)
    {
        $dateTime = new \DateTime('@' . $startDate);
        $date = Date::fromNativeDateTime($dateTime);

        return $this->with('startDate', $date);
    }

    /**
     * @param PeriodConstraint $remainingTotal
     * @return Coupon
     */
    public function withRemainingTotal(PeriodConstraint $remainingTotal)
    {
        return $this->with('remainingTotal', $remainingTotal);
    }

    /**
     * @param string $property
     * @param mixed $value
     * @return Coupon
     */
    private function with($property, $value)
    {
        $c = clone $this;
        $c->{$property} = $value;
        return $c;
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize()
    {
        $data = [
            "id" => (String) $this->getId(),
            "name" => (String) $this->getName()->toNative(),
        ];

        if (!is_null($this->description)) {
            $data['description'] = $this->getDescription()->toNative();
        }

        if (!is_null($this->expirationDate)) {
            $data['expirationDate'] = $this->getExpirationDate()
              ->toNativeDateTime()
              ->format('Y-m-d');
        }

        if (!is_null($this->startDate)) {
            $data['startDate'] = $this->getStartDate()
                ->toNativeDateTime()
                ->format('Y-m-d');
        }

        if (!is_null($this->remainingTotal)) {
            $data['remainingTotal'] = $this->getRemainingTotal()->jsonSerialize();
        }

        return $data;
    }

    /**
     * @param \CultureFeed_Uitpas_Event_TicketSale_Coupon $cfCoupon
     * @return Coupon
     */
    public static function fromCultureFeedCoupon(\CultureFeed_Uitpas_Event_TicketSale_Coupon $cfCoupon)
    {
        $coupon = new Coupon(
            new StringLiteral($cfCoupon->id),
            new StringLiteral($cfCoupon->name)
        );

        if (!empty($cfCoupon->description)) {
            $coupon = $coupon->withDescription(
                new StringLiteral($cfCoupon->description)
            );
        }

        if (!empty($cfCoupon->validTo)) {
            $coupon = $coupon->withExpirationDate(
                new Integer($cfCoupon->validTo)
            );
        }

        if (!empty($cfCoupon->validFrom)) {
            $coupon = $coupon->withStartDate(
                new Integer($cfCoupon->validFrom)
            );
        }

        if (!empty($cfCoupon->remainingTotal)) {
            $coupon = $coupon->withRemainingTotal(
                PeriodConstraint::fromCulturefeedPeriodConstraint($cfCoupon->remainingTotal)
            );
        }

        return $coupon;
    }
}
