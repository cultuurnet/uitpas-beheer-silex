<?php
/**
 * @file
 */

namespace CultuurNet\UiTPASBeheer\Activity;

use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASNumber;
use ValueObjects\Number\Integer;
use ValueObjects\StringLiteral\StringLiteral;

class SimpleQuery implements QueryInterface
{
    /**
     * @var DateType
     */
    protected $dateType;

    /**
     * @var StringLiteral
     */
    protected $query;

    /**
     * @var UiTPASNumber
     */
    protected $uitpasNumber;

    /**
     * @var string
     */
    protected $sort;

    /**
     * @var \ValueObjects\Number\Integer
     */
    protected $page;

    /**
     * @var \ValueObjects\Number\Integer
     */
    protected $limit;

    /**
     * @var StringLiteral
     */
    protected $startDate;

    /**
     * @var StringLiteral
     */
    protected $endDate;

    /**
     * @param DateType $dateType
     *
     * @return static
     */
    public function withDateType(DateType $dateType)
    {
        $c = clone $this;

        $c->dateType = $dateType;

        return $c;
    }

    /**
     * @param StringLiteral $query
     *
     * @return static
     */
    public function withQuery(StringLiteral $query)
    {
        $c = clone $this;

        $c->query = $query;

        return $c;
    }

    /**
     * @param UiTPASNumber $uitpasNumber
     *
     * @return static
     */
    public function withUiTPASNumber(UiTPASNumber $uitpasNumber)
    {
        $c = clone $this;

        $c->uitpasNumber = $uitpasNumber;

        return $c;
    }

    /**
     * @param string $sort
     *
     * @return static
     */
    public function withSort($sort)
    {
        $c = clone $this;

        $c->sort = $sort;

        return $c;
    }

    /**
     * @param Integer $page
     * @param Integer $limit
     *
     * @return static
     */
    public function withPagination(Integer $page, Integer $limit)
    {
        $c = clone $this;

        $c->page = $page;
        $c->limit = $limit;

        return $c;
    }

    /**
     * @param Integer $startDate
     * @param Integer $endDate
     *
     * @return static
     */
    public function withDateRange(Integer $startDate, Integer $endDate)
    {
        $c = clone $this;

        $c->startDate = $startDate;
        $c->endDate = $endDate;

        return $c;
    }
}
