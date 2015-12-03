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
    public function __construct(Date $creationDate, StringLiteral $title, Integer $points)
    {
        parent::__construct(
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
        $dateTime = \DateTime::createFromFormat('U', $pointsTransaction->cashingDate);
        $dateTime->setTimezone(new \DateTimeZone(date_default_timezone_get()));
        $creationDate = Date::fromNativeDateTime($dateTime);
        $title = new StringLiteral((string) $pointsTransaction->title);
        $points = new Integer($pointsTransaction->points);

        return new static(
            $creationDate,
            $title,
            $points
        );
    }
}
