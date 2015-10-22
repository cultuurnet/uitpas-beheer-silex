<?php

namespace CultuurNet\UiTPASBeheer\PassHolder\Search;

use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASNumber;
use CultuurNet\UiTPASBeheer\UiTPAS\UiTPASNumberCollection;
use ValueObjects\Number\Integer;

class Query implements QueryBuilderInterface
{
    /**
     * @var UiTPASNumberCollection|null
     */
    protected $uitpasNumbers;

    /**
     * @var \ValueObjects\Number\Integer
     */
    protected $page;

    /**
     * @var \ValueObjects\Number\Integer
     */
    protected $limit;

    public function __construct()
    {
        $this->page = new Integer(1);
        $this->limit = new Integer(10);
    }

    /**
     * @param UiTPASNumberCollection $uitpasNumbers
     * @return static
     */
    public function withUiTPASNumbers(UiTPASNumberCollection $uitpasNumbers)
    {
        $c = clone $this;
        $c->uitpasNumbers = $uitpasNumbers;
        return $c;
    }

    /**
     * @return UiTPASNumberCollection|null
     */
    public function getUiTPASNumbers()
    {
        return $this->uitpasNumbers;
    }

    /**
     * @param \ValueObjects\Number\Integer $page
     * @param \ValueObjects\Number\Integer $limit
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
     * @return \CultureFeed_Uitpas_Passholder_Query_SearchPassholdersOptions
     */
    public function build()
    {
        $searchOptions = new \CultureFeed_Uitpas_Passholder_Query_SearchPassholdersOptions();
        $searchOptions->max = $this->limit->toNative();
        $searchOptions->start = ($this->page->toNative() - 1) * $this->limit->toNative();

        if (!is_null($this->uitpasNumbers)) {
            $searchOptions->uitpasNumber = array_map(
                function (UiTPASNumber $uitpasNumber) {
                    return $uitpasNumber->toNative();
                },
                array_values($this->uitpasNumbers->toArray())
            );
        }

        return $searchOptions;
    }
}
