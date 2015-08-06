<?php

namespace CultuurNet\UiTPASBeheer\ResultSet;

use ValueObjects\Number\Integer;

abstract class AbstractPagedResultSet implements PagedResultSetInterface
{
    /**
     * @var array
     */
    protected $results;

    /**
     * @var string
     */
    protected $resultClass;

    /**
     * @var \ValueObjects\Number\Integer
     */
    protected $total;

    /**
     * @param \ValueObjects\Number\Integer $total
     * @param array $results
     * @throws \LogicException
     */
    public function __construct(Integer $total, array $results)
    {
        $this->guardResultTypes($results);
        $this->guardLogicTotal($total, $results);

        $this->total = $total;
        $this->results = $results;
    }

    /**
     * @return array
     */
    public function getResults()
    {
        return $this->results;
    }

    /**
     * @return \ValueObjects\Number\Integer
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * @param array $results
     */
    private function guardResultTypes(array $results)
    {
        foreach ($results as $result) {
            if (!is_a($result, $this->resultClass)) {
                throw new \InvalidArgumentException(
                    sprintf(
                        'Results should only be instances of %s, %s given.',
                        $this->resultClass,
                        get_class($result)
                    )
                );
            }
        }
    }

    /**
     * @param \ValueObjects\Number\Integer $total
     * @param array $results
     *
     * @throw \LogicException
     *   When the total is lower than the amount of results.
     */
    private function guardLogicTotal(Integer $total, array $results)
    {
        if (count($results) > $total->toNative()) {
            throw new \LogicException(
                'Total can not be lower than the amount of current results.'
            );
        }
    }
}
