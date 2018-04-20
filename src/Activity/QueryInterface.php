<?php
/**
 * @file
 */

namespace CultuurNet\UiTPASBeheer\Activity;

use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASNumber;
use ValueObjects\Number\Integer;
use ValueObjects\StringLiteral\StringLiteral;

/**
 * Interface for defining search queries on UiTPAS activities.
 */
interface QueryInterface
{
    /**
     * @param DateType $dateType
     *
     * @return static
     */
    public function withDateType(DateType $dateType);

    /**
     * @param StringLiteral $query
     *
     * @return static
     */
    public function withQuery(StringLiteral $query);

    /**
     * @param UiTPASNumber $uitpasNumber
     *
     * @return static
     */
    public function withUiTPASNumber(UiTPASNumber $uitpasNumber);

    /**
     * @param string $sort
     *
     * @return static
     */
    public function withSort($sort);

    /**
     * @param Integer $page
     * @param Integer $limit
     *
     * @return static
     */
    public function withPagination(Integer $page, Integer $limit);

    /**
     * @param Integer $startDate
     * @param Integer $endDate
     *
     * @return static
     */
    public function withDateRange(Integer $startDate, Integer $endDate);
}
