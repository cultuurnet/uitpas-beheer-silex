<?php
/**
 * @file
 */

namespace CultuurNet\UiTPASBeheer\Activity\CultureFeedUiTPAS;

use CultuurNet\UiTPASBeheer\Activity\DateType;
use CultuurNet\UiTPASBeheer\Activity\QueryInterface;
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
    protected $uiTPASNumber;

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
     * @param UiTPASNumber $uiTPASNumber
     *
     * @return static
     */
    public function withUiTPASNumber(UiTPASNumber $uiTPASNumber)
    {
        $c = clone $this;

        $c->uiTPASNumber = $uiTPASNumber;

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
     * @param \ValueObjects\Number\Integer $page
     * @param \ValueObjects\Number\Integer $limit
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
}
