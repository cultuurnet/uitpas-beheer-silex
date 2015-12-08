<?php

namespace CultuurNet\UiTPASBeheer\PointsTransaction;

use ValueObjects\DateTime\Date;
use ValueObjects\Number\Integer;
use ValueObjects\StringLiteral\StringLiteral;

class CashedPromotionPointsTransaction extends PointsTransaction
{

    /**
     * CashedPromotionPointsTransaction constructor.
     * @param Date $creationDate
     * @param StringLiteral $title
     * @param Integer $points
     */
    public function __construct(StringLiteral $id, Date $creationDate, StringLiteral $title, Integer $points)
    {
        // Explicitly make these points negative, as they were deducted from total points.
        $minPoints = -1 * abs($points->toNative());
        $points = new Integer($minPoints);

        parent::__construct(
            $id,
            PointsTransactionType::CASHED_PROMOTION(),
            $creationDate,
            $title,
            $points
        );
    }

    /**
     * @param \CultureFeed_Uitpas_Passholder_CashedInPointsPromotion $pointsTransaction
     * @return static
     */
    public static function fromCultureFeedCashedInPromotion(
        \CultureFeed_Uitpas_Passholder_CashedInPointsPromotion $pointsTransaction
    ) {
        $id = new StringLiteral((string) $pointsTransaction->id);
        $dateTime = \DateTime::createFromFormat('U', $pointsTransaction->cashingDate);
        $dateTime->setTimezone(new \DateTimeZone(date_default_timezone_get()));
        $creationDate = Date::fromNativeDateTime($dateTime);
        $title = new StringLiteral((string) $pointsTransaction->title);
        $points = new Integer(-1 * abs($pointsTransaction->points));

        return new static(
            $id,
            $creationDate,
            $title,
            $points
        );
    }
}
