<?php

namespace CultuurNet\UiTPASBeheer\PointsTransaction;

use ValueObjects\DateTime\Date;
use ValueObjects\Number\Integer;
use ValueObjects\StringLiteral\StringLiteral;

class CheckinPointsTransaction extends PointsTransaction
{
    /**
     * CheckinPointsTransaction constructor.
     * @param Date $creationDate
     * @param StringLiteral $title
     * @param Integer $points
     */
    public function __construct(Date $creationDate, StringLiteral $title, Integer $points)
    {
        parent::__construct(
            PointsTransactionType::CHECKIN_ACTIVITY(),
            $creationDate,
            $title,
            $points
        );
    }

    /**
     * @param \CultureFeed_Uitpas_Event_CheckinActivity $checkin
     * @return static
     */
    public static function fromCultureFeedEventCheckin(\CultureFeed_Uitpas_Event_CheckinActivity $checkin)
    {
        $dateTime = \DateTime::createFromFormat('U', $checkin->creationDate);
        $dateTime->setTimezone(new \DateTimeZone(date_default_timezone_get()));
        $creationDate = Date::fromNativeDateTime($dateTime);
        $title = new StringLiteral((string) $checkin->nodeTitle);
        $points = new Integer($checkin->points);

        return new static(
            $creationDate,
            $title,
            $points
        );
    }
}
