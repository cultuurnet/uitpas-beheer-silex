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
    public function __construct(StringLiteral $id, Date $creationDate, StringLiteral $title, Integer $points)
    {
        // Explicitly make these points positive, as they were added to the total amount of points.
        $plusPoints = abs($points->toNative());
        $points = new Integer($plusPoints);

        parent::__construct(
            $id,
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
        $id = new StringLiteral((string) $checkin->id);
        $dateTime = \DateTime::createFromFormat('U', $checkin->creationDate);
        $dateTime->setTimezone(new \DateTimeZone(date_default_timezone_get()));
        $creationDate = Date::fromNativeDateTime($dateTime);
        $title = new StringLiteral((string) $checkin->nodeTitle);
        $points = new Integer($checkin->points);

        return new static(
            $id,
            $creationDate,
            $title,
            $points
        );
    }
}
